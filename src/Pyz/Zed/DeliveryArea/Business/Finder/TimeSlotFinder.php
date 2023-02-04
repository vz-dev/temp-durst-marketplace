<?php
/**
 * Durst - project - TimeSlotFinder.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 29.08.18
 * Time: 10:08
 */

namespace Pyz\Zed\DeliveryArea\Business\Finder;

use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\ItemsPerSlotHigherThanMaxSlotsException;
use Pyz\Zed\DeliveryArea\Business\Exception\NoWeekDaySetInTimeSlotException;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;
use Pyz\Zed\DeliveryArea\Business\Model\TimeSlot;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;

class TimeSlotFinder
{
    public const LIMIT = 5;

    public const FORMAT_HOUR = 'H';
    public const FORMAT_MINUTE = 'i';
    public const ITERATIONS_LIMIT = 100;

    public const FORMATTED_STRING_FORMAT = 'D, d.m.y - H:i'; //Montag 20.05.1989 16:00 - 18:00 Uhr
    public const FORMATTED_STRING_TEMPLATE = '%s bis %s Uhr';
    public const FORMATTED_STRING_FORMAT_TIME = 'H:i';

    public const DAYS_MAP = [
        'Mon' => 'Monday',
        'Tue' => 'Tuesday',
        'Wed' => 'Wednesday',
        'Thu' => 'Thursday',
        'Fri' => 'Friday',
        'Sat' => 'Saturday',
    ];

    public const GERMAN_DAYS_MAP = [
        'Mon' => 'Montag',
        'Tue' => 'Dienstag',
        'Wed' => 'Mittwoch',
        'Thu' => 'Donnerstag',
        'Fri' => 'Freitag',
        'Sat' => 'Samstag',
    ];

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface
     */
    protected $assertionChecker;

    /** @var \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\DeliveryArea\DeliveryAreaConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface[]
     */
    protected $concreteTimeSlotSavePlugins;

    /**
     * @var \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface[]
     */
    protected $concreteTimeSlotDeletePlugins;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Model\TimeSlot
     */
    protected $timeSlot;

    /**
     * @var \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer[]
     */
    protected $prevConcreteTimeSlotTransfers;

    /**
     * TimeSlotFinder constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface $assertionChecker
     * @param \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\DeliveryArea\DeliveryAreaConfig $config
     * @param \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface[] $concreteTimeSlotSavePlugins
     * @param \Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface[] $concreteTimeSlotDeletePlugins
     * @param \Pyz\Zed\DeliveryArea\Business\Model\TimeSlot $timeSlot
     * @param \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface $salesQueryContainer
     * @param \Pyz\Zed\Tour\Business\TourFacadeInterface $tourFacade
     */
    public function __construct(
        ConcreteTimeSlotAssertionInterface $assertionChecker,
        DeliveryAreaQueryContainerInterface $queryContainer,
        DeliveryAreaConfig $config,
        array $concreteTimeSlotSavePlugins,
        array $concreteTimeSlotDeletePlugins,
        TimeSlot $timeSlot,
        SalesQueryContainerInterface $salesQueryContainer
    ) {
        $this->assertionChecker = $assertionChecker;
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->concreteTimeSlotSavePlugins = $concreteTimeSlotSavePlugins;
        $this->concreteTimeSlotDeletePlugins = $concreteTimeSlotDeletePlugins;
        $this->timeSlot = $timeSlot;
        $this->salesQueryContainer = $salesQueryContainer;
        $this->prevConcreteTimeSlotTransfers = [];
    }

    /**
     * @param array $branchIds
     * @param string $zipCode
     * @param int $maxSlots
     * @param int $itemsPerSlot
     * @return ConcreteTimeSlotTransfer[]
     * @throws ItemsPerSlotHigherThanMaxSlotsException
     * @throws NoWeekDaySetInTimeSlotException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException
     */
    public function getTimeSlotsForBranchesAndZipCode(array $branchIds, string $zipCode, int $maxSlots, int $itemsPerSlot): array
    {
        $concreteTimeSlotTransfers = [];

        $limitPerBranch = $this->getLimitPerBranch($maxSlots, count($branchIds));

        if ($itemsPerSlot > $maxSlots) {
            throw new ItemsPerSlotHigherThanMaxSlotsException(ItemsPerSlotHigherThanMaxSlotsException::MESSAGE);
        }

        foreach ($branchIds as $branchId) {
            $timeSlotItems = [];

            $timeSlotTransfers = $this
                ->timeSlot
                ->getTimeSlotsByIdBranchAndZipCode($branchId, $zipCode);

            foreach ($timeSlotTransfers as $timeSlotTransfer) {
                $concreteTimeSlotTransfer = $this
                    ->getEarliestConcreteTimeSlotByTimeSlot($timeSlotTransfer);

                $concreteTimeSlotTransfers[$branchId][] = $concreteTimeSlotTransfer;
                $this->prevConcreteTimeSlotTransfers[$concreteTimeSlotTransfer->getFkTimeSlot()] = $concreteTimeSlotTransfer;

                $timeSlotItems[$concreteTimeSlotTransfer->getFkTimeSlot()] = 1;
            }

            while (count($concreteTimeSlotTransfers[$branchId]) < $limitPerBranch) {
                if ($this->testMaxItems($timeSlotItems, $itemsPerSlot)) {
                    break;
                }

                /** @var ConcreteTimeSlotTransfer $concreteTimeSlotTransfer */
                foreach ($concreteTimeSlotTransfers[$branchId] as $concreteTimeSlotTransfer) {
                    if ($timeSlotItems[$concreteTimeSlotTransfer->getFkTimeSlot()] == $itemsPerSlot) {
                        break;
                    }

                    $nextConcreteTimeSlotTransfer = $this
                        ->getNextConcreteTimeSlotByConcreteTimeSlot($this->prevConcreteTimeSlotTransfers[$concreteTimeSlotTransfer->getFkTimeSlot()]);

                    $concreteTimeSlotTransfers[$branchId][] = $nextConcreteTimeSlotTransfer;
                    $this->prevConcreteTimeSlotTransfers[$nextConcreteTimeSlotTransfer->getFkTimeSlot()] = $nextConcreteTimeSlotTransfer;

                    $timeSlotItems[$nextConcreteTimeSlotTransfer->getFkTimeSlot()]++;
                }
            }
        }

        TimeSlotSorter::sortTimeSlotsByStart($concreteTimeSlotTransfers);
        $sortedTimeSlotArray = TimeSlotSorter::mergeBranchTimeSlotArrays($concreteTimeSlotTransfers);

        $limitedArray = array_slice($sortedTimeSlotArray, 0, $maxSlots);
        TimeSlotSorter::sortLimitedTimeSlots($limitedArray);

        return $limitedArray;
    }

    /**
     * @param int $maxSlots
     * @param int $branchCount
     *
     * @return int
     */
    protected function getLimitPerBranch(int $maxSlots, int $branchCount): int
    {
        return $maxSlots / $branchCount;
    }

    /**
     * @param array $timeSlotItems
     * @param int $itemsPerSlot
     *
     * @return bool
     */
    protected function testMaxItems(array $timeSlotItems, int $itemsPerSlot): bool
    {
        foreach ($timeSlotItems as $timeSlotItem) {
            if ($timeSlotItem < $itemsPerSlot) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param TimeSlotTransfer $timeSlotTransfer
     * @return ConcreteTimeSlotTransfer
     * @throws NoWeekDaySetInTimeSlotException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Exception
     */
    protected function getEarliestConcreteTimeSlotByTimeSlot(TimeSlotTransfer $timeSlotTransfer): ConcreteTimeSlotTransfer
    {

        $oneDay = new DateInterval('P1D');
        $now = $this->createDateTime();

        $start = clone $now;
        $startTime = DateTime::createFromFormat($this->config->getTimeFormat(), $timeSlotTransfer->getStartTime());

        $start->setTime(
            $startTime->format(self::FORMAT_HOUR),
            $startTime->format(self::FORMAT_MINUTE)
        );

        if ($start->format('D') === 'Sun') {
            $start->add($oneDay);
        }

        $weekDay = $start->format('D'); // returns e.g. 'Mon' for Monday

        // set end time
        $endTime = DateTime::createFromFormat($this->config->getTimeFormat(), $timeSlotTransfer->getEndTime());
        $end = clone $start;
        $end->setTime(
            $endTime->format(self::FORMAT_HOUR),
            $endTime->format(self::FORMAT_MINUTE)
        );

        $entity = $this->createEntity($start, $end, $timeSlotTransfer->getIdTimeSlot());

        // counter to prevent endless loops
        $counter = 0;
        $getWeekDayMethod = 'get' . self::DAYS_MAP[$weekDay];

        // find the next day that the time slot is applicable
        while ($timeSlotTransfer->$getWeekDayMethod() !== true ||
            $this->assertionChecker->isValid($entity) !== true
        ) {
            if ($counter > self::ITERATIONS_LIMIT) {
                throw new NoWeekDaySetInTimeSlotException(NoWeekDaySetInTimeSlotException::MESSAGE);
            }

            // add one day
            $start->add($oneDay);
            $weekDay = $start->format('D');
            if ($weekDay === 'Sun') {
                continue;
            }
            $getWeekDayMethod = 'get' . self::DAYS_MAP[$weekDay];

            // set end time
            $endTime = DateTime::createFromFormat($this->config->getTimeFormat(), $timeSlotTransfer->getEndTime());
            $end = clone $start;
            $end->setTime(
                $endTime->format(self::FORMAT_HOUR),
                $endTime->format(self::FORMAT_MINUTE)
            );

            $entity = $this->createEntity($start, $end, $timeSlotTransfer->getIdTimeSlot());

            $counter++;
        }

        if ($entity->isNew()) {
            $entity->save();
            $this->runConcreteTimeSlotSavePlugins($entity);
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTimeSlotTransfer
     * @throws NoWeekDaySetInTimeSlotException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException
     * @throws \Exception
     */
    protected function getNextConcreteTimeSlotByConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): ConcreteTimeSlotTransfer
    {
        $timeSlotTransfer = $this->timeSlot->getTimeSlotById($concreteTimeSlotTransfer->getFkTimeSlot());

        // define interval of one day for iteration
        $oneDay = new DateInterval('P1D');

        // initialize by now
        $begin = DateTime::createFromFormat($this->config->getDateTimeFormat(), $concreteTimeSlotTransfer->getStartTime());

        // keep now for later comparison
        $start = clone $begin;

        $start->add($oneDay);
        if ($start->format('D') === 'Sun') {
            $start->add($oneDay);
        }

        $weekDay = $start->format('D'); // returns e.g. 'Mon' for Monday

        // set end time
        $endTime = DateTime::createFromFormat($this->config->getDateTimeFormat(), $concreteTimeSlotTransfer->getEndTime());
        $end = clone $start;
        $end->setTime(
            $endTime->format(self::FORMAT_HOUR),
            $endTime->format(self::FORMAT_MINUTE)
        );

        $entity = $this->createEntity($start, $end, $concreteTimeSlotTransfer->getFkTimeSlot());

        // counter to prevent endless loops
        $counter = 0;
        $getWeekDayMethod = 'get' . self::DAYS_MAP[$weekDay];

        // find the next day that the time slot is applicable
        while ($timeSlotTransfer->$getWeekDayMethod() !== true ||
            $this->assertionChecker->isValid($entity) !== true
        ) {
            if ($counter > self::ITERATIONS_LIMIT) {
                throw new NoWeekDaySetInTimeSlotException(NoWeekDaySetInTimeSlotException::MESSAGE);
            }

            // add one day
            $start->add($oneDay);
            $weekDay = $start->format('D');
            if ($weekDay === 'Sun') {
                continue;
            }
            $getWeekDayMethod = 'get' . self::DAYS_MAP[$weekDay];

            // set end time
            $endTime = DateTime::createFromFormat($this->config->getDateTimeFormat(), $concreteTimeSlotTransfer->getEndTime());
            $end = clone $start;
            $end->setTime(
                $endTime->format(self::FORMAT_HOUR),
                $endTime->format(self::FORMAT_MINUTE)
            );

            $entity = $this->createEntity($start, $end, $concreteTimeSlotTransfer->getFkTimeSlot());

            $counter++;
        }

        if ($entity->isNew()) {
            $entity->save();
            $this->runConcreteTimeSlotSavePlugins($entity);
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return ConcreteTimeSlotTransfer
     * @throws ConcreteTimeSlotNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getConcreteTimeSlotById(int $idConcreteTimeSlot): ConcreteTimeSlotTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByIsActive(true)
            ->findOneByIdConcreteTimeSlot($idConcreteTimeSlot);

        if ($entity === null) {
            throw new ConcreteTimeSlotNotFoundException(
                sprintf(
                    ConcreteTimeSlotNotFoundException::NOT_FOUND,
                    $idConcreteTimeSlot
                )
            );
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return ConcreteTimeSlotTransfer
     * @throws ConcreteTimeSlotNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getConcreteTimeSlotByIdIgnoreActive(int $idConcreteTimeSlot): ConcreteTimeSlotTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->findOneByIdConcreteTimeSlot($idConcreteTimeSlot);

        if ($entity === null) {
            throw new ConcreteTimeSlotNotFoundException(
                sprintf(
                    ConcreteTimeSlotNotFoundException::NOT_FOUND,
                    $idConcreteTimeSlot
                )
            );
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @return ConcreteTimeSlotTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getConcreteTimeSlotsInFutureWithoutConcreteTour() : array
    {
        $now = $this->createDateTime();
        $now->setTimezone(new DateTimeZone('UTC'));

        $concreteTimeSlotEntities = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByFkConcreteTour(null)
            ->filterByStartTime($now, Criteria::GREATER_EQUAL)
            ->filterByIsActive(true)
            ->find();

        $concreteTimeSlotTransfers = [];
        foreach($concreteTimeSlotEntities as $concreteTimeSlotEntity) {
            $concreteTimeSlotTransfer = $this
                ->entityToTransfer($concreteTimeSlotEntity);
            $concreteTimeSlotTransfers[] = $concreteTimeSlotTransfer;
        }

        return $concreteTimeSlotTransfers;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param int $idTimeSlot
     * @return SpyConcreteTimeSlot
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function createEntity(DateTime $start, DateTime $end, int $idTimeSlot): SpyConcreteTimeSlot
    {
        $start = clone $start;
        $end = clone $end;
        $start->setTimezone(new DateTimeZone('UTC'));
        $end->setTimezone(new DateTimeZone('UTC'));

        return $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByStartTime($start)
            ->filterByEndTime($end)
            ->filterByFkTimeSlot($idTimeSlot)
            ->findOneOrCreate();
    }

    /**
     * @param SpyConcreteTimeSlot $entity
     * @return ConcreteTimeSlotTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function entityToTransfer(SpyConcreteTimeSlot $entity): ConcreteTimeSlotTransfer
    {
        $transfer = new ConcreteTimeSlotTransfer();
        $transfer->fromArray($entity->toArray(), true);
        $transfer->setTimeFormat($this->config->getDateTimeFormat());
        $transfer->setIdBranch($entity->getSpyTimeSlot()->getFkBranch());
        $transfer->setFormattedString($this->formatDateTimeString($entity));

        if ($this->isFirstOrder($entity->getIdConcreteTimeSlot())) {
            $transfer->setMinValue($entity->getSpyTimeSlot()->getMinValueFirst());
        } else {
            $transfer->setMinValue($entity->getSpyTimeSlot()->getMinValueFollowing());
        }

        if ($entity->getStartTime() !== null) {
            $startTime = $entity->getStartTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setStartTime($startTime->format($this->config->getDateTimeFormat()));
        }
        if ($entity->getEndTime() !== null) {
            $endTime = $entity->getEndTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setEndTime($endTime->format($this->config->getDateTimeFormat()));
        }

        return $transfer;
    }

    /**
     * @param string $time
     * @return DateTime
     */
    protected function createDateTime(string $time = 'now'): DateTime
    {
        $timeZone = $this->config->getProjectTimeZone();

        return new DateTime(
            sprintf(
                '%s %s',
                $time,
                $timeZone
            )
        );
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function isFirstOrder(int $idConcreteTimeSlot): bool
    {
        return ($this
                ->salesQueryContainer
                ->querySalesOrder()
                ->filterByFkConcreteTimeslot($idConcreteTimeSlot)
                ->count() === 0);
    }

    /**
     * @param SpyConcreteTimeSlot $entity
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function formatDateTimeString(SpyConcreteTimeSlot $entity): string
    {
        return $this
            ->createFormattedTimeSlotString($entity->getStartTime(), $entity->getEndTime());
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return string
     */
    public function createFormattedTimeSlotString(
        DateTime $start,
        DateTime $end
    ): string {
        $timeZone = new DateTimeZone($this->config->getProjectTimeZone());
        $start = clone $start;
        $start->setTimezone($timeZone);
        $end = clone $end;
        $end->setTimezone($timeZone);
        $startString = $start->format(self::FORMATTED_STRING_FORMAT);
        $endString = $end->format(self::FORMATTED_STRING_FORMAT_TIME);
        $formattedString = sprintf(
            self::FORMATTED_STRING_TEMPLATE,
            $startString,
            $endString
        );

        foreach (self::GERMAN_DAYS_MAP as $key => $day) {
            $formattedString = str_replace($key, $day, $formattedString);
        }

        return $formattedString;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @retunr void
     */
    protected function runConcreteTimeSlotSavePlugins(SpyConcreteTimeSlot $concreteTimeSlot)
    {
        foreach ($this->concreteTimeSlotSavePlugins as $savePlugin) {
            $savePlugin->save($concreteTimeSlot);
        }
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @return void
     */
    protected function runConcreteTimeSlotDeletePlugins(SpyConcreteTimeSlot $concreteTimeSlot)
    {
        foreach ($this->concreteTimeSlotDeletePlugins as $deletePlugin) {
            $deletePlugin->delete($concreteTimeSlot);
        }
    }
}
