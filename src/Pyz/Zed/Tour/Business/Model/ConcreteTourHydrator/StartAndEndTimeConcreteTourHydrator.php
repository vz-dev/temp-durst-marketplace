<?php
/**
 * Durst - project - StartAndEndTimeConcreteTourHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-01-28
 * Time: 10:30
 */

namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;

use DateTime;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;

class StartAndEndTimeConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @var \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * StartAndEndTimeConcreteTourHydrator constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface $deliveryAreaFacade
     */
    public function __construct(DeliveryAreaFacadeInterface $deliveryAreaFacade)
    {
        $this->deliveryAreaFacade = $deliveryAreaFacade;
    }

    /**
     * @param \Orm\Zed\Tour\Persistence\DstConcreteTour $concreteTourEntity
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $concreteTourTransfer
     *
     * @return void
     */
    public function hydrateConcreteTour(
        DstConcreteTour $concreteTourEntity,
        ConcreteTourTransfer $concreteTourTransfer
    ) {
        if ($concreteTourEntity->getIdConcreteTour() !== null) {
            $concreteTimeSlotEntities = $concreteTourEntity->getSpyConcreteTimeSlots();

            $concreteTimeSlotTransfers = [];

            foreach ($concreteTimeSlotEntities as $concreteTimeSlotEntity) {
                $concreteTimeSlotTransfers[] = $this
                    ->deliveryAreaFacade
                    ->convertConcreteTimeSlotEntityToTransfer($concreteTimeSlotEntity);
            }

            $startAndEnd = $this->getStartAndEndFromConcreteTimeSlots($concreteTimeSlotTransfers);

            if (array_key_exists('start', $startAndEnd) && array_key_exists('end', $startAndEnd)) {
                $concreteTourTransfer->setStartTime($startAndEnd['start']);
                $concreteTourTransfer->setEndTime($startAndEnd['end']);
            }
        }
    }

    /**
     * @param ConcreteTimeSlotTransfer[] $concreteTimeSlots
     *
     * @return array
     */
    protected function getStartAndEndFromConcreteTimeSlots(array $concreteTimeSlots): array
    {
        $times = [];
        foreach ($concreteTimeSlots as $concreteTimeSlot) {
            $times[] = DateTime::createFromFormat($concreteTimeSlot->getTimeFormat(), $concreteTimeSlot->getStartTime());
            $times[] = DateTime::createFromFormat($concreteTimeSlot->getTimeFormat(), $concreteTimeSlot->getEndTime());
        }

        if (!empty($times)) {
            return [
                'start' => min($times),
                'end' => max($times),
            ];
        }

        return [];
    }
}
