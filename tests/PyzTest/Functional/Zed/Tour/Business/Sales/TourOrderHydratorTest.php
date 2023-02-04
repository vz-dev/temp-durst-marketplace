<?php
namespace PyzTest\Functional\Zed\Tour\Business\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Tour\Business\Sales\TourOrderHydrator;
use Pyz\Zed\Tour\Persistence\TourQueryContainer;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class TourOrderHydratorTest extends \Codeception\Test\Unit
{
    protected const ID_TOUR = 114;
    protected const ID_TOUR_ITEM_STATE = 50;

    /**
     * @var \PyzTest\Functional\Zed\Tour\TourBusinessTester
     */
    protected $tester;

    /**
     * @var MockObject|\Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $tourQueryContainer;

    /**
     * @var MockObject|\Orm\Zed\Tour\Persistence\DstConcreteTourQuery
     */
    protected $dstConcreteTourQuery;

    /**
     * @var \Pyz\Zed\Tour\Business\Sales\TourOrderHydratorInterface
     */
    protected $tourOrderHydrator;

    protected function _before()
    {
        $this->tourQueryContainer = $this->createTourQueryContainerMock();
        $this->dstConcreteTourQuery = $this->createDstConcreteTourQueryMock();
        $this->tourOrderHydrator = new TourOrderHydrator($this->tourQueryContainer);
    }

    protected function _after()
    {
    }

    // tests
    public function testHydrateOrderHydratesCorrectValues()
    {
        $this->tourQueryContainer
            ->expects($this->atLeastOnce())
            ->method('queryConcreteTourByIdOrder')
            ->will($this->returnValue(
                $this->dstConcreteTourQuery
            ));

        $this->dstConcreteTourQuery
            ->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(
                (new DstConcreteTour())
                    ->setIdConcreteTour(self::ID_TOUR)
                    ->setFkStateMachineItemState(self::ID_TOUR_ITEM_STATE)
            ));

        $orderTransfer = $this
            ->tourOrderHydrator
            ->hydrateOrderByTourId((new OrderTransfer())->setIdSalesOrder(1));

        $this->assertSame(self::ID_TOUR, $orderTransfer->getFkTour());
        $this->assertSame(self::ID_TOUR_ITEM_STATE, $orderTransfer->getIdTourItemState());
    }

    public function testHydrateOrderHydratesNullWhenTourEntityDoesntExist()
    {
        $this->tourQueryContainer
            ->expects($this->atLeastOnce())
            ->method('queryConcreteTourByIdOrder')
            ->will($this->returnValue(
                $this->dstConcreteTourQuery
            ));

        $this->dstConcreteTourQuery
            ->expects($this->atLeastOnce())
            ->method('findOne')
            ->will($this->returnValue(
                null
            ));

        $orderTransfer = $this
            ->tourOrderHydrator
            ->hydrateOrderByTourId((new OrderTransfer())->setIdSalesOrder(1));

        $this->assertNull($orderTransfer->getFkTour());
        $this->assertNull($orderTransfer->getIdTourItemState());
    }

    /**
     * @return MockObject|TourQueryContainerInterface
     */
    protected function createTourQueryContainerMock()
    {
        return $this
            ->getMockBuilder(TourQueryContainer::class)
            ->setMethods(
                ['queryConcreteTourByIdOrder',]
            )->getMock();
    }

    /**
     * @return MockObject|DstConcreteTourQuery
     */
    protected function createDstConcreteTourQueryMock()
    {
        return $this
            ->getMockBuilder(DstConcreteTourQuery::class)
            ->setMethods(
                ['findOne',]
            )->getMock();
    }
}
