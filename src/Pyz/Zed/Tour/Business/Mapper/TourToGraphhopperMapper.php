<?php
/**
 * Durst - project - TourToGraphhopperMapper.php.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-19
 * Time: 17:21
 */

namespace Pyz\Zed\Tour\Business\Mapper;


use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\GraphhopperStopTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Tour\Business\Exception\TourToGraphhopperMapperNoBranchCoordinatesException;
use Pyz\Zed\Tour\Business\Exception\TourToGraphhopperMapperNoOrdersException;
use Pyz\Zed\Tour\Business\Model\ConcreteTourInterface;
use Pyz\Zed\Tour\Business\Model\TourOrderInterface;
use Pyz\Zed\Tour\TourConfig;

class TourToGraphhopperMapper
{
    /**
     * @var ConcreteTourInterface
     */
    protected $concreteTourModel;

    /**
     * @var TourOrderInterface
     */
    protected $tourOrderModel;

    /**
     * @var TourConfig
     */
    protected $tourConfig;
    /**
     * @var int
     */
    protected $totalItems;

    /**
     * TourToGraphhopperMapper constructor.
     * @param ConcreteTourInterface $concreteTourModel
     * @param TourOrderInterface $tourOrderModel
     * @param TourConfig $tourConfig
     */
    public function __construct(ConcreteTourInterface $concreteTourModel, TourOrderInterface $tourOrderModel, TourConfig $tourConfig)
    {
        $this->concreteTourModel = $concreteTourModel;
        $this->tourOrderModel = $tourOrderModel;
        $this->tourConfig = $tourConfig;
    }

    /**
     * @param int $idConcreteTour
     * @return GraphhopperTourTransfer
     * @throws TourToGraphhopperMapperNoOrdersException
     */
    public function mapTourToGraphhopper(int $idConcreteTour) : GraphhopperTourTransfer
    {
        $concreteTour = $this->concreteTourModel->getConcreteTourById($idConcreteTour);
        $orderTransfers = $this
            ->tourOrderModel
            ->getOrdersByIdConcreteTour($idConcreteTour);

        if(count($orderTransfers) === 0)
        {
            throw new TourToGraphhopperMapperNoOrdersException(
                sprintf(
                    TourToGraphhopperMapperNoOrdersException::MESSAGE,
                    $idConcreteTour
                )
            );
        }

        $graphhopperTourTransfer = $this->createGraphhopperTourTransfer();
        $graphhopperTourTransfer
            ->setTourId($concreteTour->getIdConcreteTour());

        $this->hydrateGraphhopperTourTransfer($concreteTour, $orderTransfers, $graphhopperTourTransfer);

        return $graphhopperTourTransfer;
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @param array $orderTransfers
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     */
    protected function hydrateGraphhopperTourTransfer(ConcreteTourTransfer $concreteTourTransfer, array $orderTransfers, GraphhopperTourTransfer $graphhopperTourTransfer) : GraphhopperTourTransfer
    {
        $this->hydratehydrateGraphhopperStops($concreteTourTransfer, $orderTransfers, $graphhopperTourTransfer);
        $this->hydrateGraphhopperTourStartEnd($concreteTourTransfer, $orderTransfers, $graphhopperTourTransfer);

        return $graphhopperTourTransfer;
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @param array $orderTransfers
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     * @throws TourToGraphhopperMapperNoBranchCoordinatesException
     */
    protected function hydrateGraphhopperTourStartEnd(ConcreteTourTransfer $concreteTourTransfer, array $orderTransfers, GraphhopperTourTransfer $graphhopperTourTransfer) : GraphhopperTourTransfer
    {

        $branch = $orderTransfers[0]->getBranch();

        if(empty($branch->getWarehouseLng()) || empty($branch->getWarehouseLat()))
        {
            throw new TourToGraphhopperMapperNoBranchCoordinatesException(
                sprintf(
                    TourToGraphhopperMapperNoBranchCoordinatesException::MESSAGE,
                    $branch->getName(),
                    $branch->getIdBranch()
                )
            );
        }

        $tourStart = $this
            ->createGraphhopperStopTransfer();

        $tourStart
            ->setVehicleId($concreteTourTransfer->getAbstractTour()->getFkVehicleType())
            ->setName(
                sprintf('%s %s', $branch->getStreet(), $branch->getNumber())
            )
            ->setLocationId($branch->getCity())
            ->setAddressLat($branch->getWarehouseLat())
            ->setAddressLng($branch->getWarehouseLng())
            ->setItemCount($this->totalItems);

        $tourEnd = clone $tourStart;

        $graphhopperTourTransfer->setVehicleTypeName($concreteTourTransfer->getAbstractTour()->getVehicleType()->getName());
        $graphhopperTourTransfer->setVehicleCategoryProfile($concreteTourTransfer->getAbstractTour()->getVehicleType()->getVehicleCategory()->getProfile());
        $graphhopperTourTransfer->setStartLocation($tourStart);
        $graphhopperTourTransfer->setEndLocation($tourEnd);

        return $graphhopperTourTransfer;
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @param array $orderTransfers
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     */
    protected function hydratehydrateGraphhopperStops(ConcreteTourTransfer $concreteTourTransfer, array $orderTransfers, GraphhopperTourTransfer $graphhopperTourTransfer) : GraphhopperTourTransfer
    {
        foreach ($orderTransfers as $orderTransfer){
            $items = $orderTransfer
                ->getItems()
                ->count();

            $this->totalItems += $items;

            $graphhopperStopTransfer = $this->createGraphhopperStopTransfer();

            $graphhopperStopTransfer
                ->setId($orderTransfer->getIdSalesOrder())
                ->setLocationId($orderTransfer->getShippingAddress()->getAddress1())
                ->setAddressLat($orderTransfer->getShippingAddress()->getLat())
                ->setAddressLng($orderTransfer->getShippingAddress()->getLng())
                ->setName($orderTransfer->getShippingAddress()->getAddress1())
                ->setConstraintEarliest($this->createDateTimeFromString($orderTransfer->getConcreteTimeSlot()->getStartTime()))
                ->setConstraintLatest($this->createDateTimeFromString($orderTransfer->getConcreteTimeSlot()->getEndTime()))
                ->setItemCount($items)
                ->setTimeslotId($orderTransfer->getFkConcreteTimeSlot());

            $graphhopperTourTransfer->addStops($graphhopperStopTransfer);
        }

        return $graphhopperTourTransfer;
    }

    /**
     * @return GraphhopperTourTransfer
     */
    protected function createGraphhopperTourTransfer() : GraphhopperTourTransfer
    {
        return new GraphhopperTourTransfer();
    }

    /**
     * @return GraphhopperStopTransfer
     */
    protected function createGraphhopperStopTransfer() : GraphhopperStopTransfer
    {
        return new GraphhopperStopTransfer();
    }

    /**
     * @param string $dateTimeString
     * @return DateTime
     */
    protected function createDateTimeFromString(string $dateTimeString) : DateTime
    {
        return DateTime::createFromFormat(
            'U',
            strtotime($dateTimeString),
            new DateTimeZone($this->tourConfig->getProjectTimeZone())
        );
    }
}
