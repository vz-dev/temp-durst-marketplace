<?php
/**
 * Durst - project - TimeSlotGenerator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.06.21
 * Time: 14:46
 */

namespace Pyz\Zed\GraphMasters\Business\Generator;

use DateInterval;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Generated\Shared\Transfer\GraphMastersDeliveryAreaTransfer;
use Orm\Zed\GraphMasters\Persistence\Base\DstGraphmastersTimeSlot;
use Pyz\Zed\GraphMasters\Business\Model\CategoryInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;

class TimeSlotGenerator implements TimeSlotGeneratorInterface
{
    /**
     * @var CategoryInterface
     */
    protected $categoryModel;

    /**
     * @var GraphMastersConfig
     */
    protected $config;

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * @var int[]
     */
    protected $createdTimeslotIds = [];

    /**
     * TimeSlotGenerator constructor.
     *
     * @param CategoryInterface $categoryModel
     * @param GraphMastersConfig $config
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param TouchFacadeInterface $touchFacade
     */
    public function __construct(
        CategoryInterface $categoryModel,
        GraphMastersConfig $config,
        GraphMastersQueryContainerInterface $queryContainer,
        TouchFacadeInterface $touchFacade
    ) {
        $this->categoryModel = $categoryModel;
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->touchFacade = $touchFacade;
    }

    /**
     * @return void
     */
    public function createTimeSlotsForDeliveryAreaCategory()
    {
        foreach ($this->categoryModel->getActiveDeliveryAreaCategoriesForActiveBranches() as $deliveryAreaCategoryTransfer) {
            foreach ($deliveryAreaCategoryTransfer->getDeliveryAreas() as $deliveryArea) {
                $this->createTimeSlotsForTimeSlotUntilLimit($deliveryAreaCategoryTransfer, $deliveryArea);
            }
        }
    }

    /**
     * @return void
     */
    public function createTimeSlots()
    {
        foreach ($this->categoryModel->getActiveDeliveryAreaCategoriesForActiveBranches() as $deliveryAreaCategoryTransfer) {
            foreach ($deliveryAreaCategoryTransfer->getDeliveryAreas() as $deliveryArea) {
                $this->createTimeSlotsForTimeSlotUntilLimit($deliveryAreaCategoryTransfer, $deliveryArea);
            }
        }
    }

    /**
     * @return void
     */
    public function createTimeSlotsForTimeSlotUntilLimit()
    {
        $dateToCheck = new DateTime('midnight');
        $dateToCheck->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
        $limit = new DateTime($this->config->getTimeSlotCreationLimit());

        while ($dateToCheck < $limit) {
            // todo skip sunday or times that are not interesting at all
            $this->createTimeSlotEntity($dateToCheck);

            $dateToCheck->add(new DateInterval('PT15M'));
        }

        $this
            ->insertActiveTimeSlotRecords($this->createdTimeslotIds);
    }

    /**
     * @param DateTime $date
     * @param GraphMastersDeliveryAreaCategoryTransfer $categoryTransfer
     * @param GraphMastersDeliveryAreaTransfer $deliveryAreaTransfer
     *
     * @return void
     */
    protected function createTimeSlotEntity(DateTime $date)
    {
        $end = clone $date;
        $end->add(new DateInterval('PT15M'));

        $entity = $this
            ->getTimeSlotEntity($date, $end);

        if ($entity->isNew()) {
            $entity->save();
            $this->createdTimeslotIds[] = $entity->getIdGraphmastersTimeSlot();
        }
    }

    /**
     * @param string $time
     *
     * @return int
     */
    protected function getHourFromString(string $time): int
    {
        $time = $this->stringToDateTime($time);
        return (int)$time->format('H');
    }

    /**
     * @param string $time
     *
     * @return int
     */
    protected function getMinuteFromString(string $time): int
    {
        $time = $this->stringToDateTime($time);
        return (int)$time->format('i');
    }

    /**
     * @param string $time
     *
     * @return DateTime
     */
    protected function stringToDateTime(string $time): DateTime
    {
        return DateTime::createFromFormat($this->config->getTimeFormat(), $time);
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return DstGraphmastersTimeSlot
     */
    protected function getTimeSlotEntity(DateTime $start, DateTime $end): DstGraphmastersTimeSlot
    {
        return $this
            ->queryContainer
            ->createGraphmastersTimeSlotQuery()
            ->filterByStartTime($start)
            ->filterByEndTime($end)
            ->findOneOrCreate();
    }

    /**
     * @param int[] $timeslotIds
     *
     * @return void
     */
    protected function insertActiveTimeSlotRecords(array $timeslotIds)
    {
        $this
            ->touchFacade
            ->bulkTouchSetActive('GM_TIME_SLOT', $timeslotIds);
    }
}
