<?php


namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;

use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppCustomerTransfer;
use Generated\Shared\Transfer\DriverAppOrderCommentTransfer;
use Generated\Shared\Transfer\DriverAppOrderDiscountTransfer;
use Generated\Shared\Transfer\DriverAppOrderItemTransfer;
use Generated\Shared\Transfer\DriverAppOrderTransfer;
use Generated\Shared\Transfer\DriverAppShippingAddressTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\Tour\TourClientInterface;
use Pyz\Shared\Sales\SalesConstants;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverTourRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverTourResponseInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class TourHydrator implements HydratorInterface
{
    protected const DATETIME_FORMAT = 'Y-m-d H:i:s.u';
    protected const TARGET_DATETIME_FORMAT = 'U';

    /**
     * @var \Pyz\Client\Tour\TourClientInterface
     */
    protected $tourClient;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * @var \Spryker\Yves\Money\Plugin\MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * TourHydrator constructor.
     *
     * @param \Pyz\Client\Tour\TourClientInterface $tourClient
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     * @param \Spryker\Yves\Money\Plugin\MoneyPlugin $moneyPlugin
     */
    public function __construct(
        TourClientInterface $tourClient,
        AuthClientInterface $authClient,
        MoneyPlugin $moneyPlugin
    ) {
        $this->tourClient = $tourClient;
        $this->authClient = $authClient;
        $this->moneyPlugin = $moneyPlugin;
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

        $responseObject->{DriverTourResponseInterface::KEY_AUTH_VALID} = $authenticated;

        if ($authenticated !== true) {
            $responseObject->{DriverTourResponseInterface::KEY_TOURS} = [];
            return;
        }

        $requestTransfer = new DriverAppApiRequestTransfer();

        $requestTransfer
            ->setToken($this->getToken($requestObject));

        $response = $this
            ->tourClient
            ->getToursWithOrders($requestTransfer);

        $responseObject->{DriverTourResponseInterface::KEY_TOURS} = $this->hydrateTours($response->getTours());
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return bool
     */
    protected function authenticateDriver(stdClass $requestObject): bool
    {
        $token = $requestObject
            ->{DriverTourRequestInterface::KEY_TOKEN};
        if ($token == null || trim($token) == '') {
            return false;
        }

        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($token);

        $response = $this
            ->authClient
            ->authenticateDriver($requestTransfer);

        return $response
            ->getAuthValid();
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return string
     */
    protected function getToken(stdClass $requestObject): string
    {
        return $requestObject
            ->{DriverTourRequestInterface::KEY_TOKEN};
    }

    /**
     * @param iterable|\Generated\Shared\Transfer\DriverAppTourTransfer[] $tourTransfers
     *
     * @return array
     */
    protected function hydrateTours(iterable $tourTransfers): array
    {
        $tours = [];

        foreach ($tourTransfers as $tourTransfer) {
            $tour = new stdClass();
            $tour->{DriverTourResponseInterface::KEY_TOUR_TOUR_ID} = $tourTransfer->getIdTour();
            $tour->{DriverTourResponseInterface::KEY_TOUR_IS_DELIVERABLE} = $tourTransfer->getIsDeliverable();
            $tour->{DriverTourResponseInterface::KEY_TOUR_DELIVERY_AREAS} = $tourTransfer->getDeliveryAreas();
            $tour->{DriverTourResponseInterface::KEY_TOUR_TOUR_REFERENCE} = $tourTransfer->getTourReference();
            $tour->{DriverTourResponseInterface::KEY_TOUR_TOUR_DATE} = $tourTransfer->getTourDate();
            $tour->{DriverTourResponseInterface::KEY_TOUR_TOUR_START} = $tourTransfer->getTourStart();
            $tour->{DriverTourResponseInterface::KEY_TOUR_TOUR_END} = $tourTransfer->getTourEnd();
            $tour->{DriverTourResponseInterface::KEY_TOUR_COMMENT} = $tourTransfer->getComment();
            $tour->{DriverTourResponseInterface::KEY_TOUR_MODE} = $tourTransfer->getTravelMode();
            $tour->{DriverTourResponseInterface::KEY_TOUR_WAREHOUSE_LAT} = $tourTransfer->getWarehouseLat();
            $tour->{DriverTourResponseInterface::KEY_TOUR_WAREHOUSE_LNG} = $tourTransfer->getWarehouseLng();
            $tour->{DriverTourResponseInterface::KEY_TOUR_ORDERS} = [];

            foreach ($tourTransfer->getOrders() as $order) {
                $tour->{DriverTourResponseInterface::KEY_TOUR_ORDERS}[] = $this->hydrateOrder($order);
            }

            $tours[] = $tour;
        }

        return $tours;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppOrderTransfer $orderTransfer
     *
     * @return \stdClass
     */
    protected function hydrateOrder(DriverAppOrderTransfer $orderTransfer): stdClass
    {
        $order = new stdClass();

        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ID} = $orderTransfer->getIdOrder();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_REFERENCE} = $orderTransfer->getOrderReference();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_IS_EXTERNAL} = $orderTransfer->getIsExternal();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_IS_PRIVATE} = $orderTransfer->getIsPrivate();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_TIME_SLOT_FROM} = $orderTransfer->getTimeSlotFrom();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_TIME_SLOT_TO} = $orderTransfer->getTimeSlotTo();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DELIVERY_ORDER} = $orderTransfer->getDeliveryOrder();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_NOTE} = $orderTransfer->getCustomerNote();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_PAYMENT_METHOD} = $orderTransfer->getPaymentMethod();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_PAYMENT_CODE} = $orderTransfer->getPaymentCode();
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER} = $this->hydrateCustomer($orderTransfer->getCustomer());
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS} = $this->hydrateShippingAddress($orderTransfer->getShippingAddress());
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS} = [];
        $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS} = [];

        foreach ($orderTransfer->getOrderItems() as $item) {
            $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS}[] = $this->hydrateOrderItem($item);
        }

        foreach ($orderTransfer->getComments() as $comment) {
            if($comment->getMessage() !== null && trim($comment->getMessage()) !== '') {
                $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS}[] = $this->hydrateComment($comment);
            }
        }

        $addressCommentTransfer = $orderTransfer->getShippingAddress()->getComment();
        if($addressCommentTransfer !== null){
            $addressComment = new stdClass();
            $addressComment->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS_TYPE} = SalesConstants::COMMENT_TYPE_ADDRESS;
            $addressComment->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS_MESSAGE} = $addressCommentTransfer;

            $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS}[] = $addressComment;
        }

        foreach ($orderTransfer->getDiscounts() as $discount) {
            $order->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS}[] = $this->hydrateDiscount($discount);
        }

        return $order;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppOrderCommentTransfer $commentTransfer
     * @return \stdClass
     */
    protected function hydrateComment(DriverAppOrderCommentTransfer $commentTransfer): stdClass
    {
        $comment = new stdClass();

        $comment->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS_TYPE} = $commentTransfer->getType();
        $comment->{DriverTourResponseInterface::KEY_TOUR_ORDERS_COMMENTS_MESSAGE} = $commentTransfer->getMessage();

        return $comment;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppOrderItemTransfer $orderItemTransfer
     *
     * @return \stdClass
     */
    protected function hydrateOrderItem(DriverAppOrderItemTransfer $orderItemTransfer): stdClass
    {
        $orderItem = new stdClass();

        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_ORDER_ITEM_ID} = $orderItemTransfer->getIdOrderItem();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_GTIN} = $orderItemTransfer->getGtin();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_SKU} = $orderItemTransfer->getSku();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_QUANTITY} = $orderItemTransfer->getQuantity();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_PRODUCT_NAME} = $orderItemTransfer->getProductName();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_UNIT_NAME} = $orderItemTransfer->getUnitName();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_PRICE_SINGLE} = $this->formatMoney($orderItemTransfer->getPriceSingle());
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_PRICE_TOTAL} = $this->formatMoney($orderItemTransfer->getPriceTotal());
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_GTIN_TO_ORDER_ITEM_ORDER_ITEMS_DEPOSIT_SINGLE} = $this->formatMoney($orderItemTransfer->getDepositSingle());
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_TAX_RATE} = $orderItemTransfer->getTaxRate();
        $orderItem->{DriverTourResponseInterface::KEY_TOUR_ORDERS_ORDER_ITEMS_TAX_AMOUNT} = $this->formatMoney($orderItemTransfer->getTaxAmount());

        return $orderItem;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppCustomerTransfer $customerTransfer
     *
     * @return \stdClass
     */
    protected function hydrateCustomer(DriverAppCustomerTransfer $customerTransfer): stdClass
    {
        $customer = new stdClass();

        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_SALUTATION} = $customerTransfer->getSalutation();
        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_FIRST_NAME} = $customerTransfer->getFirstName();
        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_LAST_NAME} = $customerTransfer->getLastName();
        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_EMAIL} = $customerTransfer->getEmail();
        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_COMPANY} = $customerTransfer->getCompany();
        $customer->{DriverTourResponseInterface::KEY_TOUR_ORDERS_CUSTOMER_PHONE} = $customerTransfer->getPhone();

        return $customer;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppShippingAddressTransfer $shippingAddressTransfer
     *
     * @return \stdClass
     */
    protected function hydrateShippingAddress(DriverAppShippingAddressTransfer $shippingAddressTransfer): stdClass
    {
        $shippingAddress = new stdClass();

        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_SALUTATION} = $shippingAddressTransfer->getSalutation();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_FIRST_NAME} = $shippingAddressTransfer->getFirstName();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_LAST_NAME} = $shippingAddressTransfer->getLastName();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_1} = $shippingAddressTransfer->getAddress1();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_2} = $shippingAddressTransfer->getAddress2();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ADDRESS_3} = $shippingAddressTransfer->getAddress3();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ZIP_CODE} = $shippingAddressTransfer->getZipCode();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_CITY} = $shippingAddressTransfer->getCity();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_COMPANY} = $shippingAddressTransfer->getCompany();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_PHONE} = $shippingAddressTransfer->getPhone();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_ELEVATOR} = $shippingAddressTransfer->getElevator();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_COMMENT} = $shippingAddressTransfer->getComment();
        $shippingAddress->{DriverTourResponseInterface::KEY_TOUR_ORDERS_SHIPPING_ADDRESS_FLOOR} = $shippingAddressTransfer->getFloor();

        return $shippingAddress;
    }

    /**
     * @param int $money
     *
     * @return float
     */
    protected function formatMoney(int $money): float
    {
        return $this->moneyPlugin->convertIntegerToDecimal($money);
    }

    /**
     * @param DriverAppOrderDiscountTransfer $discountTransfer
     * @return stdClass
     */
    protected function hydrateDiscount(DriverAppOrderDiscountTransfer $discountTransfer): stdClass
    {
        $discount = new stdClass();

        $discount->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS_ID} = $discountTransfer->getId();
        $discount->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS_NAME} = $discountTransfer->getName();
        $discount->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS_AMOUNT} = $discountTransfer->getAmount();
        $discount->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS_EXPENSE_TYPE} = $discountTransfer->getExpenseType();
        $discount->{DriverTourResponseInterface::KEY_TOUR_ORDERS_DISCOUNTS_MIN_SUB_TOTAL} = $discountTransfer->getMinSubTotal();

        return $discount;
    }
}
