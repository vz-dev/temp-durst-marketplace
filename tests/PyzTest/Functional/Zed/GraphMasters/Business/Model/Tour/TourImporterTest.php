<?php
/**
 * Durst - project - TourImporterTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 16.05.22
 * Time: 15:09
 */

namespace PyzTest\Functional\Zed\GraphMasters\Business\Model\Tour;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\GraphMastersCommissioningTimeTransfer;
use Generated\Shared\Transfer\GraphMastersOpeningTimeTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Service\HttpRequest\HttpRequestService;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainer;
use Pyz\Zed\GraphMasters\Business\Handler\OrderHandler;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandler;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Model\CommissioningTime;
use Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder\GraphmastersOrder;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettings;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\Business\Model\OpeningTime;
use Pyz\Zed\GraphMasters\Business\Model\Tour\Tour;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourImporter;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourInterface;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourReferenceGenerator;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourReferenceGeneratorInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacade;
use Pyz\Zed\Integra\Business\IntegraFacade;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainer;
use Pyz\Zed\Sales\Business\SalesFacade;
use Pyz\Zed\Touch\Business\TouchFacade;
use Pyz\Zed\Tour\Business\TourFacade;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacade;

class TourImporterTest extends Unit
{

    protected const MIDDAY_TOUR_IMPORT_START_TIME = '2022-05-17T14:51:38+02:00';
    protected const MIDDAY_CUTOFF_TIME = '2022-05-17T08:55:00+02:00';

    protected const LATE_TOUR_IMPORT_START_TIME = '2022-05-17T19:51:38+02:00';
    protected const LATE_CUTOFF_TIME = '2022-05-17T15:55:00+02:00';

    protected const PREV_DAY_TOUR_IMPORT_START_TIME = '2022-05-17T06:51:38+02:00';
    protected const PREV_DAY_CUTOFF_TIME = '2022-05-16T16:00:00+02:00';


    /**
     * @var GraphMastersSettingsInterface
     */
    protected $settingsModel;

    /**
     * @var TourHandlerInterface
     */
    protected $tourHandler;

    /**
     * @var TourInterface
     */
    protected $tourModel;

    /**
     * @var TourReferenceGeneratorInterface
     */
    protected $tourReferenceGenerator;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @var TourImporter
     */
    protected $tourImporter;

    protected function _before()
    {
        $this->tourImporter = new TourImporter(
            $this->settingsModel = $this->createSettingsModelMock(),
            $this->tourHandler = $this->createTourHandlerMock(),
            $this->tourModel = $this->createTourModelMock(),
            $this->tourReferenceGenerator = $this->createTourReferenceGeneratorMock(),
            $this->merchantFacade = $this->createMerchantFacadeMock(),
            $this->tourFacade = $this->createTourFacadeMock()
        );
    }

    protected function _after()
    {
        parent::_after(); // TODO: Change the autogenerated stub
    }

    public function testMidDayCommissioningTimeCutoff()
    {
        $sameDay = $this->tourImporter->getCommissioningCutOffTime(static::MIDDAY_TOUR_IMPORT_START_TIME, $this->createSettingsTransfer());

        $this
            ->assertEquals(static::MIDDAY_CUTOFF_TIME, $sameDay);
    }

    public function testLateCommissioningTimeCutoff()
    {
        $cutoff = $this->tourImporter->getCommissioningCutOffTime(static::LATE_TOUR_IMPORT_START_TIME, $this->createSettingsTransfer());

        $this
            ->assertEquals(static::LATE_CUTOFF_TIME, $cutoff);
    }

    public function testPrevDayCommissioningTimeCutoff()
    {
        $sameDay = $this->tourImporter->getCommissioningCutOffTime(static::PREV_DAY_TOUR_IMPORT_START_TIME, $this->createSettingsTransfer());

        $this
            ->assertEquals(static::PREV_DAY_CUTOFF_TIME, $sameDay);
    }


    protected function createSettingsModelMock()
    {
        return $this
            ->getMockBuilder(GraphMastersSettings::class)
            ->setConstructorArgs([
                $this->createGraphMastersQueryContainerMock(),
                $this->createOpeningTimeModelMock(),
                $this->createCommissioningTimeModelMock(),
                $this->createTouchFacadeMock()
            ])
            ->getMock();
    }

    protected function createGraphMastersQueryContainerMock()
    {
        return $this
            ->getMockBuilder(GraphMastersQueryContainer::class)
            ->getMock();
    }

    protected function createOpeningTimeModelMock()
    {
        return $this
            ->getMockBuilder(OpeningTime::class)
            ->setConstructorArgs([
                $this->createGraphMastersQueryContainerMock(),
                $this->createGraphmastersConfigMock()
            ])
            ->getMock();
    }

    protected function createCommissioningTimeModelMock()
    {
        return $this
            ->getMockBuilder(CommissioningTime::class)
            ->setConstructorArgs([
                $this->createGraphMastersQueryContainerMock(),
                $this->createGraphmastersConfigMock()
            ])
            ->getMock();
    }

    protected function createTouchFacadeMock()
    {
        return $this
            ->getMockBuilder(TouchFacade::class)
            ->getMock();
    }

    protected function createTourHandlerMock()
    {
        return $this
            ->getMockBuilder(TourHandler::class)
            ->setConstructorArgs([
                $this->createSettingsModelMock(),
                $this->createHttpRequestFacadeMock(),
                $this->createHttpRequestServiceMock(),
                $this->createGraphmastersConfigMock()
            ])
            ->getMock();
    }

    protected function createTourModelMock()
    {
        return $this
            ->getMockBuilder(Tour::class)
            ->setConstructorArgs([
                $this->createGraphMastersQueryContainerMock(),
                $this->createGraphmastersConfigMock(),
                $this->createSalesFacadeMock(),
                $this->createSettingsModelMock(),
                $this->createTourHandlerMock(),
                $this->createOrderModelMock(),
                $this->createTourFacadeMock(),
                $this->createOmsQueryContainerMock(),
                $this->createIntegraFacadeMock(),
                $this->createDiscountQueryContainerMock(),
                $this->createMerchantFacadeMock()
            ])
            ->getMock();
    }

    protected function createTourReferenceGeneratorMock()
    {
        return $this
            ->getMockBuilder(TourReferenceGenerator::class)
            ->setConstructorArgs([
                $this->createSequenceNumberFacadeMock(),
                new SequenceNumberSettingsTransfer()
            ])
            ->getMock();
    }

    protected function createMerchantFacadeMock()
    {
        return $this
            ->getMockBuilder(MerchantFacade::class)
            ->getMock();
    }

    protected function createTourFacadeMock()
    {
        return $this
            ->getMockBuilder(TourFacade::class)
            ->getMock();
    }

    protected function createGraphmastersConfigMock()
    {
        return $this
            ->getMockBuilder(GraphMastersConfig::class)
            ->getMock();
    }

    protected function createHttpRequestFacadeMock()
    {
        return $this
            ->getMockBuilder(HttpRequestFacade::class)
            ->getMock();
    }

    protected function createHttpRequestServiceMock()
    {
        return $this
            ->getMockBuilder(HttpRequestService::class)
            ->getMock();
    }

    protected function createSalesFacadeMock()
    {
        return $this
            ->getMockBuilder(SalesFacade::class)
            ->getMock();
    }

    protected function createOrderModelMock()
    {
        return $this
            ->getMockBuilder(GraphmastersOrder::class)
            ->setConstructorArgs([
                $this->createGraphMastersQueryContainerMock(),
                $this->createGraphmastersConfigMock(),
                $this->createSettingsModelMock(),
                $this->createSalesFacadeMock(),
                $this->createOrderHandlerMock()
            ])
            ->getMock();
    }

    protected function createOmsQueryContainerMock()
    {
        return $this
            ->getMockBuilder(OmsQueryContainer::class)
            ->getMock();
    }

    protected function createIntegraFacadeMock()
    {
        return $this
            ->getMockBuilder(IntegraFacade::class)
            ->getMock();
    }

    protected function createDiscountQueryContainerMock()
    {
        return $this
            ->getMockBuilder(DiscountQueryContainer::class)
            ->getMock();
    }

    protected function createOrderHandlerMock()
    {
        return $this
            ->getMockBuilder(OrderHandler::class)
            ->setConstructorArgs([
                $this->createSettingsModelMock(),
                $this->createHttpRequestFacadeMock(),
                $this->createHttpRequestServiceMock(),
                $this->createGraphmastersConfigMock()
            ])
            ->getMock();
    }

    protected function createSequenceNumberFacadeMock()
    {
        return $this
            ->getMockBuilder(SequenceNumberFacade::class)
            ->getMock();
    }

    protected function createSettingsTransfer() : GraphMastersSettingsTransfer
    {
        return (new GraphMastersSettingsTransfer())
            ->addCommissioningTimes(
                (new GraphMastersCommissioningTimeTransfer())
                    ->setWeekday('tuesday')
                    ->setStartTime('09:00')
                    ->setEndTime('11:00')
            )->addCommissioningTimes(
                (new GraphMastersCommissioningTimeTransfer())
                    ->setWeekday('tuesday')
                    ->setStartTime('12:00')
                    ->setEndTime('18:00')
            )->addCommissioningTimes(
                (new GraphMastersCommissioningTimeTransfer())
                    ->setWeekday('monday')
                    ->setStartTime('09:00')
                    ->setEndTime('11:00')
            )->addCommissioningTimes(
                (new GraphMastersCommissioningTimeTransfer())
                    ->setWeekday('monday')
                    ->setStartTime('12:00')
                    ->setEndTime('18:00')
            )
            ->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                ->setWeekday('tuesday')
                ->setStartTime('08:00')
                ->setEndTime('12:00')
            )->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                    ->setWeekday('tuesday')
                    ->setStartTime('12:00')
                    ->setEndTime('18:00')
            )->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                    ->setWeekday('tuesday')
                    ->setStartTime('18:00')
                    ->setEndTime('22:00')
            )->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                    ->setWeekday('monday')
                    ->setStartTime('08:00')
                    ->setEndTime('12:00')
            )->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                    ->setWeekday('monday')
                    ->setStartTime('12:00')
                    ->setEndTime('18:00')
            )->addOpeningTimes(
                (new GraphMastersOpeningTimeTransfer())
                    ->setWeekday('monday')
                    ->setStartTime('18:00')
                    ->setEndTime('22:00')
            )
            ->setLeadTime(120)
            ->setBufferTime(45);
    }
}