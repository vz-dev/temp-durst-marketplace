<?php


namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;


use ArrayObject;
use Generated\Shared\Transfer\CloseOrderVoucherTransfer;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;
use Generated\Shared\Transfer\ReturnDepositTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Client\Oms\OmsClientInterface;
use Pyz\Client\Sales\SalesClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverCloseOrderRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverCloseOrderResponseInterface as Response;
use stdClass;

class CloseOrderHydrator implements HydratorInterface
{
    public const ORDER_ITEM_REASON_DELIVERED = 1;
    public const ORDER_ITEM_REASON_DAMAGED = 2;
    public const ORDER_ITEM_REASON_DECLINED = 3;
    public const ORDER_ITEM_REASON_LOST = 4;

    /**
     * @var \Pyz\Client\Sales\SalesClientInterface
     */
    protected $salesClient;

    /**
     * @var \Pyz\Client\Merchant\MerchantClientInterface
     */
    protected $merchantClient;

    /**
     * @var \Pyz\Client\Oms\OmsClientInterface
     */
    protected $omsClient;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Generated\Shared\Transfer\OrderTransfer
     */
    protected $orderTransfer;

    /**
     * @var DriverTransfer
     */
    protected $driver;

    /**
     * CloseOrderHydrator constructor.
     * @param SalesClientInterface $salesClient
     * @param MerchantClientInterface $merchantClient
     * @param OmsClientInterface $omsClient
     * @param AuthClientInterface $authClient
     * @param AppRestApiConfig $config
     */
    public function __construct(
        SalesClientInterface $salesClient,
        MerchantClientInterface $merchantClient,
        OmsClientInterface $omsClient,
        AuthClientInterface $authClient,
        AppRestApiConfig $config
    )
    {
        $this->salesClient = $salesClient;
        $this->merchantClient = $merchantClient;
        $this->omsClient = $omsClient;
        $this->authClient = $authClient;
        $this->config = $config;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $authenticated = $this
            ->authenticateDriver($requestObject);

        $responseObject->{Response::KEY_AUTH_VALID} = $authenticated;
        $responseObject->{Response::KEY_ORDER_ALREADY_CLOSED} = false;

        $checkOrder = $this
            ->checkOrder($requestObject);

        $checkOrderItems = $this
            ->assertAllOrderItemsAreClosed($requestObject);

        if ($authenticated !== true || $checkOrder !== true) {
            $responseObject->{Response::KEY_ORDER_CLOSED} = false;
            return;
        }

        if($this->assertAllItemsAreReadyForDelivery() !== true)
        {
            $responseObject->{Response::KEY_ORDER_CLOSED} = false;
            $responseObject->{Response::KEY_ORDER_ALREADY_CLOSED} = true;
            return;
        }

        if ($checkOrderItems !== true) {
            $responseObject->{Response::KEY_ORDER_CLOSED} = false;
            return;
        }

        $responseObject->{Response::KEY_ORDER_CLOSED} = true;

        $dataTransfer = $this->prepareData($requestObject);

        $response = $this
            ->omsClient
            ->triggerRecalculateEventForOrderItems($dataTransfer);

        if ($response->getHasError() === true) {
            $responseObject->{Response::KEY_ORDER_CLOSED} = false;
            $responseObject->{Response::KEY_ERROR_MESSAGE} = $response->getErrorMessage();

        }
    }

    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function authenticateDriver(stdClass $requestObject): bool
    {
        $token = $requestObject
            ->{Request::KEY_TOKEN};
        if($token == null || trim($token) == ''){
            return false;
        }
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($this->getToken($requestObject));

        $response = $this
            ->authClient
            ->authenticateDriver($requestTransfer);

        return $response
            ->getAuthValid();
    }

    /**
     * @param string $token
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function getDriverByToken(string $token): DriverTransfer
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($token);

        return $this
            ->authClient
            ->getDriverByToken($requestTransfer);
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getToken(stdClass $requestObject): string
    {
        return $requestObject
            ->{Request::KEY_TOKEN};
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer
     */
    protected function prepareData(stdClass $requestObject): OrderRefundReturnDepositFormDataTransfer
    {
        $voucherTransfer = new CloseOrderVoucherTransfer();

        if (isset($requestObject->{Request::KEY_VOUCHER}) === true) {
            $voucher = $requestObject->{Request::KEY_VOUCHER};

            if (
                isset($voucher->{Request::KEY_VOUCHER_SALES_DISCOUNT_ID}) === true &&
                isset($voucher->{Request::KEY_VOUCHER_AMOUNT}) === true
            ) {
                $voucherTransfer
                    ->setIdSalesDiscount($voucher->{Request::KEY_VOUCHER_SALES_DISCOUNT_ID})
                    ->setAmount($voucher->{Request::KEY_VOUCHER_AMOUNT});
            }
        }

        $dataTransfer = (new OrderRefundReturnDepositFormDataTransfer())
            ->setItems(new ArrayObject($this->prepareItems($requestObject, null)))
            ->setItemsDelivered(new ArrayObject($this->prepareItems($requestObject, self::ORDER_ITEM_REASON_DELIVERED)))
            ->setItemsDamaged(new ArrayObject($this->prepareItems($requestObject, self::ORDER_ITEM_REASON_DAMAGED)))
            ->setItemsDeclined(new ArrayObject($this->prepareItems($requestObject, self::ORDER_ITEM_REASON_DECLINED)))
            ->setItemsLost(new ArrayObject($this->prepareItems($requestObject, self::ORDER_ITEM_REASON_LOST)))
            ->setBranch($this->orderTransfer->getFkBranch())
            ->setOrigOrderItems($this->orderTransfer->getItems())
            ->setReturnDeposits(new ArrayObject($this->prepareReturnDeposits($requestObject)))
            ->setRefundComment('')
            ->setSignature($requestObject->{Request::KEY_SIGNATURE_IMAGE})
            ->setIsReseller($this->getIsReseller($requestObject))
            ->setHasError(false)
            ->setErrorMessage('')
            ->setVoucher($voucherTransfer)
            ->setDriver($this->driver->getIdDriver());

        if(isset($requestObject->{Request::KEY_SIGNED_AT}) === true){
            $dataTransfer->setSignedAt($requestObject->{Request::KEY_SIGNED_AT});
        }

        if(isset($requestObject->{Request::KEY_EXTERNAL_AMOUNT_PAID}) === true){
            $dataTransfer->setExternalAmountPaid($requestObject->{Request::KEY_EXTERNAL_AMOUNT_PAID});
        }

        return $dataTransfer;
    }

    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function getIsReseller(stdClass $requestObject): bool
    {
        return (
            property_exists($requestObject, Request::KEY_IS_RESELLER) === true &&
            $requestObject->{Request::KEY_IS_RESELLER} === true
        );
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return \Generated\Shared\Transfer\ReturnDepositTransfer[]
     */
    protected function prepareReturnDeposits(stdClass $requestObject): array
    {
        $returnedDeposits = [];
        foreach ($requestObject->{Request::KEY_RETURNED_DEPOSITS} as $returnedDeposit) {
            $returnedDeposits[] = (new ReturnDepositTransfer())
                ->setDepositId($returnedDeposit->{Request::KEY_RETURNED_DEPOSITS_DEPOSIT_ID})
                ->setDeposit($returnedDeposit->{Request::KEY_RETURNED_DEPOSITS_DEPOSIT})
                ->setBottles($returnedDeposit->{Request::KEY_RETURNED_DEPOSITS_BOTTLES})
                ->setCases($returnedDeposit->{Request::KEY_RETURNED_DEPOSITS_CASES});
        }

        return $returnedDeposits;
    }

    /**
     * @param stdClass $requestObject
     * @param int|null $reason
     * @return array
     */
    protected function prepareItems(stdClass $requestObject, ?int $reason): array
    {
        $returnedItems = [];

        foreach ($this->orderTransfer->getItems() as $item) {
            $returnItem = (new ItemTransfer())
                ->setQuantity($item->getQuantity())
                ->setIdSalesOrderItem($item->getIdSalesOrderItem())
                ->setMerchantSku($item->getMerchantSku())
                ->setSku($item->getSku());
            foreach ($requestObject->{Request::KEY_ORDER_ITEMS} as $requestObjectItem) {
                if ($item->getIdSalesOrderItem() !== $requestObjectItem->{Request::KEY_ORDER_ITEMS_ORDER_ITEM_ID}) {
                    continue;
                }

                if($reason)
                {
                    if ($requestObjectItem->{Request::KEY_ORDER_ITEMS_STATUS} === $reason) {
                        $quantity = $returnItem->getQuantity() - $requestObjectItem->{Request::KEY_ORDER_ITEMS_QUANTITY};
                        $returnItem->setQuantity($quantity);

                        if($quantity < 0){
                            $returnItem->setQuantity(0);
                        }

                        $returnedItems[] = $returnItem;
                    }

                    continue;
                }

                if ($requestObjectItem->{Request::KEY_ORDER_ITEMS_STATUS} !== 1) {
                    $quantity = $returnItem->getQuantity() - $requestObjectItem->{Request::KEY_ORDER_ITEMS_QUANTITY};
                    $returnItem->setQuantity($quantity);

                    if($quantity < 0){
                        $returnItem->setQuantity(0);
                    }

                    $returnedItems[] = $returnItem;
                }
            }

        }

        return $returnedItems;
    }

    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function checkOrder(stdClass $requestObject): bool
    {
        $this->driver = $this
            ->getDriverByToken($this->getToken($requestObject));

        $idOrder = $requestObject->{Request::KEY_ORDER_ID};

        $this->orderTransfer = $this
            ->salesClient
            ->getOrderByIdSalesOrder($idOrder);

        return ($this->orderTransfer
                ->getBranch()
                ->getIdBranch() === $this->driver->getFkBranch());
    }

    /**
     * @param stdClass $requestObject
     * @return bool
     */
    protected function assertAllOrderItemsAreClosed(stdClass $requestObject): bool
    {
        $origOrderItemIds = [];
        foreach ($this->orderTransfer->getItems() as $orderItem){
            $origOrderItemIds[] = $orderItem->getIdSalesOrderItem();
        }

        $closedIds = [];
        foreach ($requestObject->{Request::KEY_ORDER_ITEMS} as $requestObjectItem) {
            $closedIds[] = $requestObjectItem->{Request::KEY_ORDER_ITEMS_ORDER_ITEM_ID};
        }

        return sort($origOrderItemIds) == sort($closedIds);
    }

    /**
     * @return bool
     */
    protected function assertAllItemsAreReadyForDelivery() : bool
    {
        foreach ($this->orderTransfer->getItems() as $orderItem){
            if($orderItem->getState()->getName() !== $this->config->getOmsStateReadyForDelivery())
            {
                return false;
            }
        }

        return true;
    }
}
