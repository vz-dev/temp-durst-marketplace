<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 08.12.18
 * Time: 16:51
 */

namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\Tour\TourConfig;

class StatusConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @var TourConfig
     */
    protected $config;

    /**
     * StatusConcreteTourHydrator constructor.
     * @param TourConfig $config
     */
    public function __construct(
        TourConfig $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function hydrateConcreteTour(
        DstConcreteTour $concreteTourEntity,
        ConcreteTourTransfer $concreteTourTransfer
    )
    {
        $now = $this->createDateTime();

        $deliveryDate = $concreteTourEntity->getDate()->format($this->config->getDateFormat());

        $abstractTour = $concreteTourTransfer->getAbstractTour();

        if ($abstractTour !== null){
            $startTime = $abstractTour->getStartTime();
            $endTime = $abstractTour->getEndTime();

            $loadingTime = 0;
            $approachTime = 0;

            if (($loadingTime!== null) && ($approachTime !== null)){
                $preparationMinutes = $loadingTime + $approachTime;

                if (($startTime!== null) && ($endTime !== null)){
                    $deliveryStartTime = $this->createDateTime($deliveryDate .' ' .$startTime);
                    $deliveryEndTime = $this->createDateTime($deliveryDate .' ' .$endTime);

                    $preparationStartTime = $this->getModifiedTime($deliveryStartTime, $preparationMinutes);
                    $orderableEndTime = $preparationStartTime;

                    $prepTimeBufferMinutesBeforeStart = $abstractTour->getPrepTimeBufferMinutesBeforeStart();
                    if (($prepTimeBufferMinutesBeforeStart !== null) && ($prepTimeBufferMinutesBeforeStart > $preparationMinutes)){
                        $orderableEndTime = $this->getModifiedTime($deliveryStartTime, $prepTimeBufferMinutesBeforeStart);
                    }

                    $concreteTourTransfer->setStatus(
                        $this->getConcreteTourStatusByTimes(
                            $orderableEndTime,
                            $preparationStartTime,
                            $deliveryStartTime,
                            $deliveryEndTime,
                            $now)
                    );
                }

            }
        }
    }

    /**
     * @param \DateTime $deliveryStartTime
     * @param int $preparationMinutes
     * @return \DateTime
     */
    protected function getModifiedTime(\DateTime $time, int $minutes) : \DateTime
    {
        $modifyString = sprintf(
            $this->config::PREPARATION_MODIFY_FORMAT,
            $minutes
        );
        $modifiedTime = clone $time;
        $modifiedTime->modify($modifyString);

        return $modifiedTime;
    }

    /**
     * @param \DateTime $preparationStartTime
     * @param \DateTime $deliveryStartTime
     * @param \DateTime $deliveryEndTime
     * @param \DateTime $now
     * @return string
     */
    protected function getConcreteTourStatusByTimes(
        \DateTime $orderableEndTime,
        \DateTime $preparationStartTime,
        \DateTime $deliveryStartTime,
        \DateTime $deliveryEndTime,
        \DateTime $nowTime
    ) : string
    {
        $deliveryEnd = $deliveryEndTime->format('U');
        $deliveryStart = $deliveryStartTime->format('U');
        $preparationStart = $preparationStartTime->format('U');
        $orderableEnd = $orderableEndTime->format('U');
        $now = $nowTime->format('U');

        $status = $this->config::CONCRETE_TOUR_STATUS_LABELS;

        if ($now > $deliveryEnd)
        {
            return $status[TourConstants::CONCRETE_TOUR_STATUS_DELIVERED];
        }
        if ($now > $deliveryStart)
        {
            return $status[TourConstants::CONCRETE_TOUR_STATUS_IN_DELIVERY];
        }
        if ($now > $preparationStart)
        {
            return $status[TourConstants::CONCRETE_TOUR_STATUS_DELIVERABLE];
        }
        if (($now < $preparationStart) && ($now > $orderableEnd))
        {
            return $status[TourConstants::CONCRETE_TOUR_STATUS_PLANABLE];
        }
        return $status[TourConstants::CONCRETE_TOUR_STATUS_ORDERABLE];
    }

    /**
     * @param string $time
     * @return \DateTime
     */
    public function createDateTime(string $time = 'now') : \DateTime
    {
        $timeZone = $this->config->getProjectTimeZone();

        return new \DateTime(
            sprintf(
                '%s %s',
                $time,
                $timeZone
            )
        );
    }
}
