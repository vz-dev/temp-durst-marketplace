<?php
/**
 * Durst - project - LatLngOrderAddressSaver.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-06
 * Time: 10:30
 */

namespace Pyz\Zed\Graphhopper\Business\Checkout;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Graphhopper\Business\Model\GeocodingInterface;
use Pyz\Zed\Graphhopper\Persistence\GraphhopperQueryContanerInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;

class LatLngOrderAddressSaver implements LatLngOrderAddressSaverInterface
{
    public const ADDRESS_STRING_FORMAT = '%s %s %s';

    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @var GeocodingInterface
     */
    protected $geocoding;

    /**
     * LatLngOrderAddressSaver constructor.
     * @param SalesQueryContainerInterface $salesQueryContainer
     * @param GeocodingInterface $geocoding
     */
    public function __construct(
        SalesQueryContainerInterface $salesQueryContainer,
        GeocodingInterface $geocoding
    )
    {
        $this->salesQueryContainer = $salesQueryContainer;
        $this->geocoding = $geocoding;
    }


    /**
     * @param AddressTransfer $addressTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function saveLatLngToAddress(AddressTransfer $addressTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $salesOrderEntity = $this->getOrderEntity($saveOrderTransfer->getIdSalesOrder());
        $shippingAddress = $salesOrderEntity->getShippingAddress();

        $addressString = sprintf(
            self::ADDRESS_STRING_FORMAT,
            $addressTransfer->getAddress1(),
            $addressTransfer->getZipCode(),
            $addressTransfer->getCity()
        );

        $graphhopperCoordinatesTransfer = $this->geocoding->getCoordinatesForAddressString($addressString);
        $shippingAddress->setLat($graphhopperCoordinatesTransfer->getLat());
        $shippingAddress->setLng($graphhopperCoordinatesTransfer->getLng());

        $shippingAddress->save();
    }

    /**
     * @param int $orderId
     * @return SpySalesOrder
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getOrderEntity(int $orderId) : SpySalesOrder
    {
        return $this
            ->salesQueryContainer
            ->QuerySalesOrder()
                ->filterByIdSalesOrder($orderId)
            ->findOne();
    }
}
