<?php
/**
 * Durst - project - TourToGraphhopperMapperTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-06
 * Time: 20:53
 */

namespace PyzTest\Functional\Zed\Tour\Business\Mapper;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Generated\Shared\Transfer\VehicleCategoryTransfer;
use Generated\Shared\Transfer\VehicleTypeTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacade;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainer;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Integra\Business\IntegraFacade;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Oms\Persistence\OmsQueryContainer;
use Pyz\Zed\Sales\Business\SalesFacade;
use Pyz\Zed\Tour\Business\Exception\TourToGraphhopperMapperNoBranchCoordinatesException;
use Pyz\Zed\Tour\Business\Exception\TourToGraphhopperMapperNoOrdersException;
use Pyz\Zed\Tour\Business\Mapper\Product\ProductRepository;
use Pyz\Zed\Tour\Business\Mapper\Product\ProductRepositoryInterface;
use Pyz\Zed\Tour\Business\Mapper\TourDriverAppMapper;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Business\Mapper\TourToGraphhopperMapper;
use Pyz\Zed\Tour\Business\Model\ConcreteTour;
use Pyz\Zed\Tour\Business\Model\TourOrder;
use Pyz\Zed\Tour\Business\Model\TourReferenceGenerator;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridge;
use Pyz\Zed\Tour\Persistence\TourQueryContainer;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacade;
use Spryker\Zed\StateMachine\Business\StateMachineFacade;

class TourToGraphhopperMapperTest extends \Codeception\Test\Unit
{

    public const TOUR_ID = 1;
    public const VEHICLE_TYPE_ID = 2;
    public const VEHICLE_TYPE_NAME = 'VW Caddy';
    public const VEHICLE_CATEGORY_PROFILE = 'car';

    public const NUM_OF_TEST_ORDERS = 3;

    public const CS_TIME_STRING = '2019-12-10T21:00:00+01:00';
    public const CS_TIMESTAMP = 1576008000;
    public const TIMEZONE = 'Europe/Berlin';

    public const BRANCH_STREET = 'Oskar Jäger Straße';
    public const BRANCH_STREET_NO = '173';
    public const BRANCH_CITY = 'Köln';
    public const BRANCH_WH_LAT = '1.234567';
    public const BRANCH_WH_LNG = '8.90123456';

    public const TEST_ORDER_TRANSFER_1_ID_SALES_ORDER = 10;
    public const TEST_ORDER_TRANSFER_1_ORDER_REF = 'DE--10';
    public const TEST_ORDER_TRANSFER_1_ADDRESS = 'Oskar Jäger Str 173';
    public const TEST_ORDER_TRANSFER_1_LAT ='5.5678';
    public const TEST_ORDER_TRANSFER_1_LNG = '40.40004';
    public const TEST_ORDER_TRANSFER_1_START_TIME = 3;
    public const TEST_ORDER_TRANSFER_1_END_TIME = 3;
    public const TEST_ORDER_TRANSFER_1_ITEM_COUNT = 3;
    public const TEST_ORDER_TRANSFER_1_TS_ID = 80;

    public const TEST_ORDER_TRANSFER_2_ID_SALES_ORDER = 20;
    public const TEST_ORDER_TRANSFER_2_ORDER_REF = 'DE--11';
    public const TEST_ORDER_TRANSFER_2_ADDRESS = 'Oskar Jäger Str 173';
    public const TEST_ORDER_TRANSFER_2_LAT ='5.5678';
    public const TEST_ORDER_TRANSFER_2_LNG = '40.40004';
    public const TEST_ORDER_TRANSFER_2_START_TIME = 3;
    public const TEST_ORDER_TRANSFER_2_END_TIME = 3;
    public const TEST_ORDER_TRANSFER_2_ITEM_COUNT = 5;
    public const TEST_ORDER_TRANSFER_2_TS_ID = 90;

    public const TEST_ORDER_TRANSFER_3_ID_SALES_ORDER = 10;
    public const TEST_ORDER_TRANSFER_3_ORDER_REF = 'DE--12';
    public const TEST_ORDER_TRANSFER_3_ADDRESS = 'Oskar Jäger Str 173';
    public const TEST_ORDER_TRANSFER_3_LAT ='5.5678';
    public const TEST_ORDER_TRANSFER_3_LNG = '40.40004';
    public const TEST_ORDER_TRANSFER_3_START_TIME = 3;
    public const TEST_ORDER_TRANSFER_3_END_TIME = 3;
    public const TEST_ORDER_TRANSFER_3_ITEM_COUNT = 50;
    public const TEST_ORDER_TRANSFER_3_TS_ID = 100;

    public const TOTAL_ORDER_ITEMS = self::TEST_ORDER_TRANSFER_1_ITEM_COUNT + self::TEST_ORDER_TRANSFER_2_ITEM_COUNT + self::TEST_ORDER_TRANSFER_3_ITEM_COUNT;

    /**
     * @var ConcreteTour|MockObject
     */
    protected $concreteTourModel;

    /**
     * @var TourOrder|MockObject
     */
    protected $tourOrderModel;

    /**
     * @var TourConfig|MockObject
     */
    protected $tourConfig;
    /**
     * @var TourToGraphhopperMapper
     */
    protected $tourToGraphhopperMapper;

    protected function _before()
    {
        $this->concreteTourModel = $this->createConcreteTourModelMock();
        $this->tourOrderModel = $this->createTourOrderModelMock();
        $this->tourConfig = $this->createTourConfigMock();

        $this->tourToGraphhopperMapper = new TourToGraphhopperMapper(
            $this->concreteTourModel,
            $this->tourOrderModel,
            $this->tourConfig
        );
    }

    protected function _after()
    {
    }

    public function testGraphhopperTransferCorrectAmountOfStops()
    {
        $this
            ->concreteTourModel
            ->expects($this->once())
            ->method('getConcreteTourById')
            ->willReturn(
                $this->createConcreteTourTransfer()
            );

        $this
            ->tourOrderModel
            ->expects($this->once())
            ->method('getOrdersByIdConcreteTour')
            ->willReturn(
                $this->createTestOrderTransfers()
            );
        $this
            ->tourConfig
            ->expects($this->atLeastOnce())
            ->method('getProjectTimeZone')
            ->willReturn(
                static::TIMEZONE
            );

        $ghTour = $this->tourToGraphhopperMapper->mapTourToGraphhopper(self::TOUR_ID);
    }

    public function testThrowsTourToGraphhopperMapperNoOrdersException()
    {
        $this
            ->expectException(TourToGraphhopperMapperNoOrdersException::class);

        $this
            ->concreteTourModel
            ->expects($this->once())
            ->method('getConcreteTourById')
            ->willReturn(
                $this->createConcreteTourTransfer()
            );

        $this
            ->tourOrderModel
            ->expects($this->once())
            ->method('getOrdersByIdConcreteTour')
            ->willReturn(
                []
            );

        $ghTour = $this->tourToGraphhopperMapper->mapTourToGraphhopper(self::TOUR_ID);
    }

    public function testThrowsTourToGraphhopperMapperNoBranchCoordinatesException()
    {
        $this
            ->expectException(TourToGraphhopperMapperNoBranchCoordinatesException::class);

        $this
            ->concreteTourModel
            ->expects($this->once())
            ->method('getConcreteTourById')
            ->willReturn(
                $this->createConcreteTourTransfer()
            );

        $this
            ->tourOrderModel
            ->expects($this->once())
            ->method('getOrdersByIdConcreteTour')
            ->willReturn(
                $this->createTestOrderTransfersNoBranchCoordinates()
            );

        $this
            ->tourConfig
            ->expects($this->atLeastOnce())
            ->method('getProjectTimeZone')
            ->willReturn(
                static::TIMEZONE
            );

        $this->tourToGraphhopperMapper->mapTourToGraphhopper(self::TOUR_ID);
    }

    public function testGraphhopperTransferHasStartAndStopLocations()
    {
        $this
            ->concreteTourModel
            ->expects($this->once())
            ->method('getConcreteTourById')
            ->willReturn(
                $this->createConcreteTourTransfer()
            );

        $this
            ->tourOrderModel
            ->expects($this->once())
            ->method('getOrdersByIdConcreteTour')
            ->willReturn(
                $this->createTestOrderTransfers()
            );

        $this
            ->tourConfig
            ->expects($this->atLeastOnce())
            ->method('getProjectTimeZone')
            ->willReturn(
                static::TIMEZONE
            );

        $ghTour = $this->tourToGraphhopperMapper->mapTourToGraphhopper(self::TOUR_ID);

        $this->assertNotEmpty($ghTour->getStartLocation());
        $this->assertNotEmpty($ghTour->getEndLocation());
    }


    public function testGraphhopperTransferValuesMappedCorrectly()
    {
        $this
            ->concreteTourModel
            ->expects($this->once())
            ->method('getConcreteTourById')
            ->willReturn(
                $this->createConcreteTourTransfer()
            );

        $this
            ->tourOrderModel
            ->expects($this->once())
            ->method('getOrdersByIdConcreteTour')
            ->willReturn(
                $this->createTestOrderTransfers()
            );

        $this
            ->tourConfig
            ->expects($this->atLeastOnce())
            ->method('getProjectTimeZone')
            ->willReturn(
                static::TIMEZONE
            );

        $ghTour = $this->tourToGraphhopperMapper->mapTourToGraphhopper(self::TOUR_ID);

        $this->assertSame(self::TOUR_ID, $ghTour->getTourId());

        /***
         * Start Location Assertions
         */
        $this->assertSame(static::VEHICLE_TYPE_ID, $ghTour->getStartLocation()->getVehicleId());
        $this->assertSame(
            sprintf('%s %s',
                static::BRANCH_STREET,
                static::BRANCH_STREET_NO
            ),
            $ghTour->getStartLocation()->getName()
        );
        $this->assertSame(static::BRANCH_CITY, $ghTour->getStartLocation()->getLocationId());
        $this->assertSame(static::BRANCH_WH_LAT, $ghTour->getStartLocation()->getAddressLat());
        $this->assertSame(static::BRANCH_WH_LNG, $ghTour->getStartLocation()->getAddressLng());
        $this->assertSame(static::TOTAL_ORDER_ITEMS, $ghTour->getStartLocation()->getItemCount());

        /***
         * End Location Assertions
         */
        $this->assertSame(static::VEHICLE_TYPE_ID, $ghTour->getEndLocation()->getVehicleId());
        $this->assertSame(
            sprintf('%s %s',
                static::BRANCH_STREET,
                static::BRANCH_STREET_NO
            ),
            $ghTour->getEndLocation()->getName()
        );
        $this->assertSame(static::BRANCH_CITY, $ghTour->getEndLocation()->getLocationId());
        $this->assertSame(static::BRANCH_WH_LAT, $ghTour->getEndLocation()->getAddressLat());
        $this->assertSame(static::BRANCH_WH_LNG, $ghTour->getEndLocation()->getAddressLng());
        $this->assertSame(static::TOTAL_ORDER_ITEMS, $ghTour->getEndLocation()->getItemCount());

        /***
         * Assertions for each stop
         */
        for($i=0; $i < static::NUM_OF_TEST_ORDERS; $i++)
        {
            $tour = $ghTour->getStops()[$i];

            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_ID_SALES_ORDER'), $tour->getId());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_ADDRESS'), $tour->getName());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_ADDRESS'), $tour->getLocationId());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_LAT'), $tour->getAddressLat());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_LNG'), $tour->getAddressLng());
            $this->assertSame(static::CS_TIMESTAMP, $tour->getConstraintEarliest()->getTimestamp());
            $this->assertSame(static::CS_TIMESTAMP, $tour->getConstraintLatest()->getTimestamp());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_TS_ID'), $tour->getTimeslotId());
            $this->assertSame(constant('static::TEST_ORDER_TRANSFER_'.($i+1).'_ITEM_COUNT'), $tour->getItemCount());
        }
    }

    /**
     * @return MockObject|ConcreteTour
     */
    protected function createConcreteTourModelMock()
    {
        return $this
            ->getMockBuilder(ConcreteTour::class)
            ->setConstructorArgs([
                $this->createTourQueryContainerMock(),
                $this->createTourReferenceGeneratorMock(),
                $this->createTourConfigMock(),
                [],
                $this->createDeliveryAreaFacadeMock(),
                $this->createIntegraFacadeMock(),
                $this->createTourToStateMachineBridgeMock()
            ])
            ->setMethods(
                ['getConcreteTourById']
            )->getMock();
    }

    protected function createTourQueryContainerMock()
    {
        return $this
            ->getMockBuilder(TourQueryContainer::class)
            ->getMock();
    }

    protected function createTourReferenceGeneratorMock()
    {
        return $this
            ->getMockBuilder(TourReferenceGenerator::class)
            ->setConstructorArgs([
                $this->createSequenceNumberFacadeMock(),
                $this->createSequenceNumberSettingsTransferMock()
            ])
            ->getMock();
    }

    protected function createDeliveryAreaFacadeMock()
    {
        return $this
            ->getMockBuilder(DeliveryAreaFacade::class)
            ->getMock();
    }

    protected function createTourToStateMachineBridgeMock()
    {
        return $this
            ->getMockBuilder(TourToStateMachineBridge::class)
            ->setConstructorArgs([
                $this->createStateMachineFacadeMock()
            ])
            ->getMock();
    }

    protected function createSequenceNumberFacadeMock()
    {
        return $this
            ->getMockBuilder(SequenceNumberFacade::class)
            ->getMock();
    }

    protected function createSequenceNumberSettingsTransferMock()
    {
        return $this
            ->getMockBuilder(SequenceNumberSettingsTransfer::class)
            ->getMock();
    }

    protected function createStateMachineFacadeMock()
    {
        return $this
            ->getMockBuilder(StateMachineFacade::class)
            ->getMock();
    }

    /**
     * @return MockObject|TourOrder
     */
    protected function createTourOrderModelMock()
    {
        return $this
            ->getMockBuilder(TourOrder::class)
            ->setConstructorArgs([
                $this->createTourQueryContainerMock(),
                $this->createOmsQueryContainerMock(),
                $this->createMerchantFacadeMock(),
                $this->createSalesFacadeMock(),
                $this->createConcreteTourModelMock(),
                $this->createTourConfigMock(),
                $this->createTourDriverAppMapperMock(),
                $this->createIntegraFacadeMock(),
                $this->createDiscountQueryContainerMock(),
            ])
            ->setMethods(
                ['getOrdersByIdConcreteTour',]
            )->getMock();
    }

    protected function createOmsQueryContainerMock()
    {
        return $this
            ->getMockBuilder(OmsQueryContainer::class)
            ->getMock();
    }

    protected function createMerchantFacadeMock()
    {
        return $this
            ->getMockBuilder(MerchantFacade::class)
            ->getMock();
    }

    protected function createSalesFacadeMock()
    {
        return $this
            ->getMockBuilder(SalesFacade::class)
            ->getMock();
    }

    /**
     * @return MockObject|TourConfig
     */
    protected function createTourConfigMock()
    {
        return $this
            ->getMockBuilder(TourConfig::class)
            ->setMethods(
                ['getProjectTimeZone']
            )
            ->getMock();
    }

    /**
     * @return MockObject|TourDriverappMapperInterface
     */
    protected function createTourDriverAppMapperMock(): TourDriverappMapperInterface
    {
        return $this
            ->getMockBuilder(TourDriverAppMapper::class)
            ->setConstructorArgs([
                $this->createMerchantFacadeMock(),
                $this->createProductRepositoryMock(),
                $this->createTourConfigMock(),
            ])
            ->getMock();
    }

    /**
     * @return MockObject|ProductRepositoryInterface
     */
    protected function createProductRepositoryMock(): ProductRepositoryInterface
    {
        return $this
            ->getMockBuilder(ProductRepository::class)
            ->setConstructorArgs([
                $this->createTourQueryContainerMock(),
            ])
            ->getMock();
    }

    /**
     * @return MockObject|IntegraFacadeInterface
     */
    protected function createIntegraFacadeMock(): IntegraFacadeInterface
    {
        return $this
            ->getMockBuilder(IntegraFacade::class)
            ->getMock();
    }

    /**
     * @return MockObject|DiscountQueryContainerInterface
     */
    protected function createDiscountQueryContainerMock(): DiscountQueryContainerInterface
    {
        return $this
            ->getMockBuilder(DiscountQueryContainer::class)
            ->getMock();
    }

    /**
     * @return OrderTransfer
     */
    protected function createOrderTransfer() : OrderTransfer
    {
        $orderTransfer = new OrderTransfer();
        $orderTransfer
            ->setShippingAddress(new AddressTransfer());
        $orderTransfer
            ->setConcreteTimeSlot(new ConcreteTimeSlotTransfer());
        $orderTransfer
            ->setBranch(new BranchTransfer());

        return $orderTransfer;
    }

    /**
     * @return OrderTransfer[]
     */
    protected function createTestOrderTransfers() : array
    {
        $orderTransfers = [];

        for($i=1; $i <= self::NUM_OF_TEST_ORDERS; $i++)
        {
            $orderTransfer = $this->createOrderTransfer();

            $orderTransfer
                ->setIdSalesOrder(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ID_SALES_ORDER'))
                ->setOrderReference(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ORDER_REF'))
                ->setFkConcreteTimeslot(constant('static::TEST_ORDER_TRANSFER_'.$i.'_TS_ID'));

            $orderTransfer
                ->getShippingAddress()
                ->setAddress1(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ADDRESS'))
                ->setLat(constant('static::TEST_ORDER_TRANSFER_'.$i.'_LAT'))
                ->setLng(constant('static::TEST_ORDER_TRANSFER_'.$i.'_LNG'));
            $orderTransfer
                ->getConcreteTimeSlot()
                ->setStartTime(static::CS_TIME_STRING)
                ->setEndTime(static::CS_TIME_STRING);
            $orderTransfer
                ->getBranch()
                ->setStreet(static::BRANCH_STREET)
                ->setNumber(static::BRANCH_STREET_NO)
                ->setCity(static::BRANCH_CITY)
                ->setWarehouseLat(static::BRANCH_WH_LAT)
                ->setWarehouseLng(static::BRANCH_WH_LNG);

            for($j=0; $j < constant('static::TEST_ORDER_TRANSFER_'.$i.'_ITEM_COUNT'); $j++)
            {
                $orderTransfer
                    ->addItem(new ItemTransfer());
            }


            $orderTransfers[] = $orderTransfer;
        }

        return $orderTransfers;
    }

    protected function createTestOrderTransfersNoBranchCoordinates() : array
    {
        $orderTransfers = [];

        for($i=1; $i <= self::NUM_OF_TEST_ORDERS; $i++)
        {
            $orderTransfer = $this->createOrderTransfer();

            $orderTransfer
                ->setIdSalesOrder(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ID_SALES_ORDER'))
                ->setOrderReference(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ORDER_REF'))
                ->setFkConcreteTimeslot(constant('static::TEST_ORDER_TRANSFER_'.$i.'_TS_ID'));

            $orderTransfer
                ->getShippingAddress()
                ->setAddress1(constant('static::TEST_ORDER_TRANSFER_'.$i.'_ADDRESS'))
                ->setLat(constant('static::TEST_ORDER_TRANSFER_'.$i.'_LAT'))
                ->setLng(constant('static::TEST_ORDER_TRANSFER_'.$i.'_LNG'));
            $orderTransfer
                ->getConcreteTimeSlot()
                ->setStartTime(static::CS_TIME_STRING)
                ->setEndTime(static::CS_TIME_STRING);
            $orderTransfer
                ->getBranch()
                ->setStreet(static::BRANCH_STREET)
                ->setNumber(static::BRANCH_STREET_NO)
                ->setCity(static::BRANCH_CITY);

            for($j=0; $j < constant('static::TEST_ORDER_TRANSFER_'.$i.'_ITEM_COUNT'); $j++)
            {
                $orderTransfer
                    ->addItem(new ItemTransfer());
            }


            $orderTransfers[] = $orderTransfer;
        }

        return $orderTransfers;
    }

    /**
     * @return ConcreteTourTransfer
     */
    protected function createConcreteTourTransfer() : ConcreteTourTransfer
    {
        $concreteTourTransfer = new ConcreteTourTransfer();
        $concreteTourTransfer
            ->setAbstractTour(
                new AbstractTourTransfer()
            );
        $concreteTourTransfer->getAbstractTour()->setFkVehicleType(static::VEHICLE_TYPE_ID);

        $vehicleCategoryTransfer = (new VehicleCategoryTransfer())
            ->setProfile(static::VEHICLE_CATEGORY_PROFILE);

        $vehicleTypeTransfer = (new VehicleTypeTransfer())
            ->setName(static::VEHICLE_TYPE_NAME)
            ->setVehicleCategory($vehicleCategoryTransfer);

        $concreteTourTransfer->getAbstractTour()->setVehicleType($vehicleTypeTransfer);

        $concreteTourTransfer->setIdConcreteTour(static::TOUR_ID);
        return $concreteTourTransfer;
    }
}
