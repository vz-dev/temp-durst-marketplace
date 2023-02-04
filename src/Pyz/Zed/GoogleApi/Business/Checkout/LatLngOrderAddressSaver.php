<?php
/**
 * Durst - project - LatLngOrderAddressSaver.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 08:16
 */

namespace Pyz\Zed\GoogleApi\Business\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GoogleApi\Business\Geocoder\GeocoderInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class LatLngOrderAddressSaver implements LatLngOrderAddressSaverInterface
{
    public const ADDRESS_STRING_FORMAT = '%s %s %s';

    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @var GeocoderInterface
     */
    protected $geocoder;

    /**
     * LatLngOrderAddressSaver constructor.
     * @param SalesQueryContainerInterface $salesQueryContainer
     * @param GeocoderInterface $geocoder
     */
    public function __construct(
        SalesQueryContainerInterface $salesQueryContainer,
        GeocoderInterface $geocoder
    )
    {
        $this->salesQueryContainer = $salesQueryContainer;
        $this->geocoder = $geocoder;
    }


    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function saveLatLngToAddress(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $salesOrderEntity = $this->getOrderEntity($saveOrderTransfer->getIdSalesOrder());
        $shippingAddress = $salesOrderEntity->getShippingAddress();

        $addressString = sprintf(
            self::ADDRESS_STRING_FORMAT,
            $quoteTransfer->getShippingAddress()->getAddress1(),
            $quoteTransfer->getShippingAddress()->getZipCode(),
            $quoteTransfer->getShippingAddress()->getCity()
        );

        $googleApiCoordinatesTransfer = $this->geocoder->getCoordinatesForAddressString($addressString, $quoteTransfer->getShippingAddress()->getZipCode());
        $shippingAddress->setLat($googleApiCoordinatesTransfer->getLat());
        $shippingAddress->setLng($googleApiCoordinatesTransfer->getLng());

        $shippingAddress->save();

        $quoteTransfer->getShippingAddress()->setLat($googleApiCoordinatesTransfer->getLat());
        $quoteTransfer->getShippingAddress()->setLng($googleApiCoordinatesTransfer->getLng());
    }

    /**
     * @param int $orderId
     * @return SpySalesOrder
     * @throws AmbiguousComparisonException
     */
    protected function getOrderEntity(int $orderId) : SpySalesOrder
    {
        return $this
            ->salesQueryContainer
            ->querySalesOrder()
            ->filterByIdSalesOrder($orderId)
            ->findOne();
    }
}
