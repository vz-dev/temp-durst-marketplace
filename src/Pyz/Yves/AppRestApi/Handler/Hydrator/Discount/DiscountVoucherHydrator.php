<?php
/**
 * Durst - project - DiscountCartItemsHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 15:21
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Discount;


use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Client\Discount\DiscountClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DiscountKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DiscountKeyResponseInterface;
use stdClass;

class DiscountVoucherHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\Discount\DiscountClientInterface
     */
    protected $client;

    /**
     * @var \Pyz\Client\Cart\CartClientInterface
     */
    protected $cartClient;

    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiClientInterface
     */
    protected $apiClient;

    /**
     * @var \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface
     */
    protected $deliveryAreaClient;

    /**
     * DiscountVoucherHydrator constructor.
     * @param \Pyz\Client\Discount\DiscountClientInterface $client
     * @param \Pyz\Client\Cart\CartClientInterface $cartClient
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $apiClient
     * @param \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface $deliveryAreaClient
     */
    public function __construct(
        DiscountClientInterface $client,
        CartClientInterface $cartClient,
        AppRestApiClientInterface $apiClient,
        DeliveryAreaClientInterface $deliveryAreaClient
    )
    {
        $this->client = $client;
        $this->cartClient = $cartClient;
        $this->apiClient = $apiClient;
        $this->deliveryAreaClient = $deliveryAreaClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $discountRequestTransfer = $this
            ->createDiscountRequestTransfer(
                $requestObject
            );

        $response = $this
            ->client
            ->checkValidVoucher(
                $discountRequestTransfer
            );

        $responseObject
            ->{DiscountKeyResponseInterface::KEY_VALID} = $response->getValid();
        $responseObject
            ->{DiscountKeyResponseInterface::KEY_ERROR_MESSAGE} = $response->getErrorMessage();
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\DiscountApiRequestTransfer
     */
    protected function createDiscountRequestTransfer(stdClass $requestObject): DiscountApiRequestTransfer
    {
        $discountRequestTransfer = new DiscountApiRequestTransfer();

        $shippingAddress = $this
            ->hydrateShippingAddress($requestObject);

        $cartItems = $this
            ->hydrateCart($requestObject);

        $discountRequestTransfer
            ->setIdBranch($requestObject->{DiscountKeyRequestInterface::KEY_BRANCH_ID})
            ->setIdTimeSlot($requestObject->{DiscountKeyRequestInterface::KEY_TIME_SLOT_ID})
            ->setVoucherCode($requestObject->{DiscountKeyRequestInterface::KEY_VOUCHER_CODE})
            ->setItems($cartItems)
            ->setShippingAddress($shippingAddress);

        return $discountRequestTransfer;
    }

    /**
     * @param \stdClass $requestObject
     * @return \ArrayObject|ItemTransfer[]
     */
    protected function hydrateCart(stdClass $requestObject): ArrayObject
    {
        $cart = $this
            ->createItemTransfers(
                $requestObject
            );

        $branch = $this
            ->getBranch(
                $requestObject
            );

        $concreteTimeSlot = $this
            ->getConcreteTimeSlot(
                $requestObject
            );

        $quoteTransfer = $this
            ->cartClient
            ->addItemsForBranchAndConcreteTimeSlot(
                $cart,
                $branch,
                $concreteTimeSlot,
                $requestObject
            );

        return $quoteTransfer
            ->getItems();
    }

    /**
     * @param \stdClass $requestObject
     * @return array|ItemTransfer[]
     */
    protected function createItemTransfers(stdClass $requestObject): array
    {
        $cartItems = $requestObject
            ->{DiscountKeyRequestInterface::KEY_CART};

        $cart = [];

        foreach ($cartItems as $cartItem) {
            $item = (new ItemTransfer())
                ->setSku(
                    $cartItem
                        ->{DiscountKeyRequestInterface::KEY_CART_SKU}
                )
                ->setQuantity(
                    $cartItem
                        ->{DiscountKeyRequestInterface::KEY_CART_QUANTITY}
                )
                ->setUnitPrice(
                    1900
                );

            $cart[] = $item;
        }

        return $cart;
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    protected function getBranch(stdClass $requestObject): BranchTransfer
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch(
                $requestObject
                    ->{DiscountKeyRequestInterface::KEY_BRANCH_ID}
            );

        return $this
            ->apiClient
            ->getBranchById(
                $requestTransfer
            );
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    protected function getConcreteTimeSlot(stdClass $requestObject): ConcreteTimeSlotTransfer
    {
        return $this
            ->deliveryAreaClient
            ->getConcreteTimeSlotById(
                $requestObject
                    ->{DiscountKeyRequestInterface::KEY_TIME_SLOT_ID}
            );
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function hydrateShippingAddress(stdClass $requestObject): AddressTransfer
    {
        $address = $requestObject
            ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS};

        $addressTransfer = (new AddressTransfer())
            ->setSalutation(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_SALUTATION}
            )
            ->setFirstName(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_FIRST_NAME}
            )
            ->setLastName(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_LAST_NAME}
            )
            ->setAddress1(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ADDRESS_1}
            )
            ->setAddress2(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ADDRESS_2}
            )
            ->setAddress3(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ADDRESS_3}
            )
            ->setZipCode(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ZIP_CODE}
            )
            ->setCity(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_CITY}
            )
            ->setCompany(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_COMPANY}
            )
            ->setPhone(
                $address
                    ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_PHONE}
            );

        if (isset($address->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_COMMENT}) === true) {
            $addressTransfer
                ->setComment(
                    $address
                        ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_COMMENT}
                );
        }

        if (property_exists($address, DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_LAT) === true) {
            $addressTransfer
                ->setLat(
                    $address
                        ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_LAT}
                );
        }

        if (property_exists($address, DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_LNG) === true) {
            $addressTransfer
                ->setLng(
                    $address
                        ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_LNG}
                );
        }

        if (property_exists($address, DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_FLOOR) === true) {
            $addressTransfer
                ->setFloor(
                    $address
                        ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_FLOOR}
                );
        }

        if (property_exists($address, DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ELEVATOR) === true) {
            $addressTransfer
                ->setElevator(
                    $address
                        ->{DiscountKeyRequestInterface::KEY_SHIPPING_ADDRESS_ELEVATOR}
                );
        }

        return $addressTransfer;
    }
}
