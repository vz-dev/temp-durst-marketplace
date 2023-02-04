<?php
/**
 * Durst - project - TimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-06
 * Time: 10:34
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Overview;


use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OverviewKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\OverviewKeyResponseInterface;
use stdClass;

class TimeSlotHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * TimeSlotHydrator constructor.
     * @param AppRestApiClientInterface $client
     */
    public function __construct(
        AppRestApiClientInterface $client
    )
    {
        $this->client = $client;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $idConcreteTimeSlot = $requestObject
            ->{OverviewKeyRequestInterface::KEY_TIME_SLOT_ID};

        if ($idConcreteTimeSlot === null || is_int($idConcreteTimeSlot) === false) {
            $responseObject->{OverviewKeyResponseInterface::KEY_TIME_SLOT} = new stdClass();
            return;
        }

        $concreteTimeSlotTransfer = $this
            ->getTimeSlotById(
                $idConcreteTimeSlot
            );

        if ($concreteTimeSlotTransfer === null) {
            $responseObject->{OverviewKeyResponseInterface::KEY_TIME_SLOT} = new stdClass();
            return;
        }

        $this
            ->hydrateTimeSlot(
                $responseObject,
                $concreteTimeSlotTransfer
            );
    }

    /**
     * @param int $idTimeSlot
     * @return ConcreteTimeSlotTransfer|null
     */
    protected function getTimeSlotById(int $idTimeSlot): ?ConcreteTimeSlotTransfer
    {
        $concreteTimeSlotTransfers = $this
            ->client
            ->getTimeSlotsByIds(
                [
                    $idTimeSlot
                ]
            );

        if (count($concreteTimeSlotTransfers) < 1) {
            return null;
        }

        $concreteTimeSlotTransfer = reset(
            $concreteTimeSlotTransfers
        );

        return $concreteTimeSlotTransfer;
    }

    /**
     * @param stdClass $responseObject
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return void
     */
    protected function hydrateTimeSlot(stdClass $responseObject, ConcreteTimeSlotTransfer $concreteTimeSlotTransfer): void
    {
        $timeslot = new stdClass();

        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_ID} = $concreteTimeSlotTransfer->getIdConcreteTimeSlot();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_MERCHANT_ID} = $concreteTimeSlotTransfer->getIdBranch();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_FROM} = $concreteTimeSlotTransfer->getStartTime();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_TO} = $concreteTimeSlotTransfer->getEndTime();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_TIME_SLOT_STRING} = $concreteTimeSlotTransfer->getFormattedString();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_START_RAW} = $concreteTimeSlotTransfer->getStartTimeRaw();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_END_RAW} = $concreteTimeSlotTransfer->getEndTimeRaw();
        $timeslot
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT_MESSAGE} = $concreteTimeSlotTransfer->getMessage();

        $responseObject
            ->{OverviewKeyResponseInterface::KEY_TIME_SLOT} = $timeslot;
    }
}
