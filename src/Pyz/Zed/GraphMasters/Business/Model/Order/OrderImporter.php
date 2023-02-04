<?php
/**
 * Durst - project - OrderImporter.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.05.21
 * Time: 14:55
 */

namespace Pyz\Zed\GraphMasters\Business\Model\Order;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\GraphMastersApiAddressTransfer;
use Generated\Shared\Transfer\GraphMastersApiGeoLocationTransfer;
use Generated\Shared\Transfer\GraphMastersApiLoadTransfer;
use Generated\Shared\Transfer\GraphMastersApiOrderUpdateTransfer;
use Generated\Shared\Transfer\GraphMastersApiShipmentTransfer;
use Generated\Shared\Transfer\GraphMastersApiTimeSlotTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Handler\OrderHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Model\CategoryInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

class OrderImporter implements OrderImporterInterface
{
    /**
     * @var GraphMastersSettingsInterface
     */
    protected $settings;

    /**
     * @var CategoryInterface
     */
    protected $categoryModel;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var OrderHandlerInterface
     */
    protected $orderHandler;

    /**
     * @var int
     */
    protected $idSalesOrder;


    /**
     * @var GraphMastersSettingsTransfer
     */
    protected $branchSettings;

    /**
     * OrderImporter constructor.
     * @param GraphMastersSettingsInterface $settings
     * @param SalesFacadeInterface $salesFacade
     * @param OrderHandlerInterface $orderHandler
     */
    public function __construct(
        GraphMastersSettingsInterface $settings,
        SalesFacadeInterface $salesFacade,
        OrderHandlerInterface $orderHandler,
        CategoryInterface $categoryModel
    )
    {
        $this->settings = $settings;
        $this->salesFacade = $salesFacade;
        $this->orderHandler = $orderHandler;
        $this->categoryModel = $categoryModel;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrder
     * @return void
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function importOrder(QuoteTransfer $quoteTransfer, int $idSalesOrder) : void
    {
        $this->branchSettings = $this->settings->getSettingsByIdBranch($quoteTransfer->getFkBranch());

        if(
            $this->settings->doesBranchUseGraphmasters($quoteTransfer->getFkBranch()) === true &&
            $this->categoryModel->getDeliversByZipAndIdBranch($quoteTransfer->getShippingAddress()->getZipCode(), $quoteTransfer->getFkBranch())
        )
        {
            $this->idSalesOrder = $idSalesOrder;

            $orderUpdateTransfer = $this
                ->createOrderUpdateTransfer($quoteTransfer);

            $this
                ->orderHandler
                ->importOrder($orderUpdateTransfer);
        }
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return GraphMastersApiOrderUpdateTransfer
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function createOrderUpdateTransfer(QuoteTransfer $quoteTransfer): GraphMastersApiOrderUpdateTransfer
    {
        // todo schöner machen ;)
        if($quoteTransfer->getUseFlexibleTimeSlots()){
            $start = $quoteTransfer->getStartTime();
            $end = $quoteTransfer->getEndTime();
        }else{
            $start = $quoteTransfer->getConcreteTimeSlots()->offsetGet(0)->getStartTime();
            $end = $quoteTransfer->getConcreteTimeSlots()->offsetGet(0)->getEndTime();
        }


        $orderTransfer = $this->salesFacade->getDeflatedOrderByIdSalesOrder($this->idSalesOrder);

        $orderUpdateTransfer = new GraphMastersApiOrderUpdateTransfer();
        $orderUpdateTransfer->setId($orderTransfer->getOrderReference());
        $orderUpdateTransfer->setDepotId($this->branchSettings->getDepotApiId());
        $orderUpdateTransfer->setStatus('open');
        $orderUpdateTransfer->setCustomerUuid(hash('sha3-512' , $orderTransfer->getDurstCustomerReference().$quoteTransfer->getShippingAddress()->getAddress1()));

        $addressParts = $this->splitStreetAndHouseNo($quoteTransfer->getShippingAddress()->getAddress1());

        $addressTransfer = new GraphMastersApiAddressTransfer();
        $addressTransfer->setStreet(trim($addressParts[1]));
        $addressTransfer->setHouseNumber(trim($addressParts[2]));
        $addressTransfer->setZipCode($quoteTransfer->getShippingAddress()->getZipCode());
        $addressTransfer->setCity($quoteTransfer->getShippingAddress()->getCity());
        $addressTransfer->setCountry('Germany');

        $orderUpdateTransfer->setAddress($addressTransfer);

        $geoLocationTransfer = new GraphMastersApiGeoLocationTransfer();
        $geoLocationTransfer->setLat($quoteTransfer->getShippingAddress()->getLat());
        $geoLocationTransfer->setLng($quoteTransfer->getShippingAddress()->getLng());

        $orderUpdateTransfer->setGeoLocation($geoLocationTransfer);
        $orderUpdateTransfer->setDateOfDelivery($start);

        $timeSlotTransfer = new GraphMastersApiTimeSlotTransfer();
        $timeSlotTransfer->setStartTime($start);
        $timeSlotTransfer->setEndTime($end);

        $orderUpdateTransfer->setTimeSlot($timeSlotTransfer);
        $orderUpdateTransfer->setStopTimeMinutes(15);

        $shipmentTransfer = new GraphMastersApiShipmentTransfer();

        $loadTransfer = new GraphMastersApiLoadTransfer();
        $loadTransfer->setItemCount($this->getItemCount($quoteTransfer));
        $loadTransfer->setWeightKilogram($this->getWeight($quoteTransfer));
        $loadTransfer->setVolumeCubicMeter(null);

        $shipmentTransfer->setLoad($loadTransfer);
        $shipmentTransfer->setRecipient('');
        $shipmentTransfer->setSender($orderTransfer->getBranch()->getName());
        $shipmentTransfer->setLabel($this->getLabelFromAddress($quoteTransfer->getShippingAddress()));
        $shipmentTransfer->setBarcode(null);

        $orderUpdateTransfer->setShipment($shipmentTransfer);

        return $orderUpdateTransfer;
    }

    /**
     * @param string $address
     * @return array
     */
    protected function splitStreetAndHouseNo(string $address) : array
    {
        $result = [
            1 => $address,
            2 => ''
        ];

        if ( preg_match('/([^\d]+)\s?(.+)/i', $address, $matches) )
        {
            return $matches;
        }

        return $result;
    }


    /**
     * @param QuoteTransfer $quoteTransfer
     * @return int
     */
    protected function getItemCount(QuoteTransfer $quoteTransfer) : int
    {
        $itemCount = 0;

        foreach($quoteTransfer->getItems() as $item){
            $itemCount += $item->getQuantity();
        }

        return $itemCount;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return float
     */
    protected function getWeight(QuoteTransfer $quoteTransfer) : float
    {
        $weight = 0;

        foreach($quoteTransfer->getItems() as $item){
            $weight += $item->getDeposit()->getWeight() * $item->getQuantity();
        }

        return ($weight / 1000);
    }

    /**
     * @param AddressTransfer $addressTransfer
     * @return string
     */
    protected function getLabelFromAddress(AddressTransfer $addressTransfer)
    {
        return sprintf(
            '%s, %s %s, Germany',
            $addressTransfer->getAddress1(),
            $addressTransfer->getZipCode(),
            $addressTransfer->getCity()
        );
    }
}
