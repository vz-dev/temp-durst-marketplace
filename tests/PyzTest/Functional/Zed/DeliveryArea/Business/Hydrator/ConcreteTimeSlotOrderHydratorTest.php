<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Hydrator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Absence\Business\AbsenceFacade;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Hydrator\ConcreteTimeSlotOrderHydrator;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\AbsenceAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxCustomersAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxPayloadAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxProductsAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\PrepTimeAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\AssertionChecker;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainer;
use Pyz\Zed\Deposit\Business\DepositFacade;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\ConcreteTimeSlotPostDeleteTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\DeliveryArea\ConcreteTimeSlotsPostSaveTouchPlugin;
use Pyz\Zed\Tour\Business\TourFacade;

class ConcreteTimeSlotOrderHydratorTest extends Unit
{
    protected const VALID_CONCRETE_TIME_SLOT_ID = 1;
    protected const INVALID_CONCRETE_TIME_SLOT_ID = 9999;

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Hydrator\ConcreteTimeSlotOrderHydrator
     */
    protected $hydrator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->hydrator = new ConcreteTimeSlotOrderHydrator(
            $this->createConcreteTimeSlotModel()
        );
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException
     */
    public function testValidConcreteTimeSlotIsSetOnOrder(): void
    {
        $order = $this
            ->createOrderTransferForIdConcreteTimeSlot(self::VALID_CONCRETE_TIME_SLOT_ID);

        $this
            ->hydrator
            ->hydrateOrderTransferWithConcreteTimeSlotTransfer($order);

        $concreteTimeSlot = $order
            ->getConcreteTimeSlot();

        $this
            ->assertEquals(
                self::VALID_CONCRETE_TIME_SLOT_ID,
                $concreteTimeSlot->getIdConcreteTimeSlot()
            );
    }

    /**
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException
     */
    public function testInvalidConcreteTimeSlotThrowsConcreteTimeSlotNotFoundException(): void
    {
        $order = $this
            ->createOrderTransferForIdConcreteTimeSlot(self::INVALID_CONCRETE_TIME_SLOT_ID);

        $this
            ->expectException(
                ConcreteTimeSlotNotFoundException::class
            );

        $this
            ->hydrator
            ->hydrateOrderTransferWithConcreteTimeSlotTransfer($order);
    }

    /**
     * @return \Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlot
     */
    protected function createConcreteTimeSlotModel(): ConcreteTimeSlot
    {
        $deliveryAreaConfig = new DeliveryAreaConfig();

        $concreteTimeSlot = new ConcreteTimeSlot(
            new DeliveryAreaQueryContainer(),
            $deliveryAreaConfig,
            new AssertionChecker(
                [
                    new MaxCustomersAssertion($deliveryAreaConfig),
                    new PrepTimeAssertion(),
                    new AbsenceAssertion(new AbsenceFacade())
                ]
            ),
            new MaxPayloadAssertion(
                new DepositFacade(),
                new DeliveryAreaConfig()
            ),
            new MaxProductsAssertion($deliveryAreaConfig),
            [
                new ConcreteTimeSlotsPostSaveTouchPlugin()
            ],
            [
                new ConcreteTimeSlotPostDeleteTouchPlugin()
            ],
            new TourFacade()
        );

        return $concreteTimeSlot;
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function createOrderTransferForIdConcreteTimeSlot(int $idConcreteTimeSlot): OrderTransfer
    {
        $orderTransfer = (new OrderTransfer())
            ->setFkConcreteTimeslot($idConcreteTimeSlot);

        return $orderTransfer;
    }
}
