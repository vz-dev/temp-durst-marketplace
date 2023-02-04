<?php
/**
 * Durst - project - QuoteHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 12:58
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Client\Sales\SalesClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\QuoteHydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\OrderKeyResponseInterface as Response;
use Spryker\Client\Checkout\CheckoutClientInterface;
use stdClass;

class QuoteHydrator implements HydratorInterface, QuoteHydratorInterface
{
    /**
     * @var QuoteHydratorInterface[]
     */
    protected $hydrators = [];

    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var DeliveryAreaClientInterface
     */
    protected $deliveryAreaClient;

    /**
     * @var CartClientInterface
     */
    protected $cartClient;

    /**
     * @var CheckoutClientInterface
     */
    protected $checkoutClient;

    /**
     * @var SalesClientInterface
     */
    protected $salesClient;

    /**
     * QuoteHydrator constructor.
     * @param QuoteHydratorInterface[] $hydrators
     * @param AppRestApiClientInterface $client
     * @param DeliveryAreaClientInterface $deliveryAreaClient
     * @param CartClientInterface $cartClient
     * @param CheckoutClientInterface $checkoutClient
     */
    public function __construct(array $hydrators, AppRestApiClientInterface $client, DeliveryAreaClientInterface $deliveryAreaClient, CartClientInterface $cartClient, CheckoutClientInterface $checkoutClient, SalesClientInterface $salesClient)
    {
        $this->hydrators = $hydrators;
        $this->client = $client;
        $this->deliveryAreaClient = $deliveryAreaClient;
        $this->cartClient = $cartClient;
        $this->checkoutClient = $checkoutClient;
        $this->salesClient = $salesClient;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     * @return mixed|void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $branch = $this->getBranch($requestObject);

        $concreteTimeSlotTransfer = $this
            ->getConcreteTimeSlot($requestObject);

        if($concreteTimeSlotTransfer !== null)
        {
            $quoteTransfer = $this
                ->cartClient
                ->addItemsForBranchAndConcreteTimeSlot(
                    $this->getItems($requestObject),
                    $branch,
                    $concreteTimeSlotTransfer,
                    $requestObject
                );
        }else{
            $quoteTransfer = $this
                ->cartClient
                ->addItemsForBranchFlexTimeSlots(
                    $this->getItems($requestObject),
                    $branch,
                    $requestObject
                );
        }

        $quoteTransfer->setUseFlexibleTimeSlots($this->useFlexibleTimeSlots($branch, $requestObject));

        $this->hydrateQuote($quoteTransfer, $requestObject);

        $checkoutResponseTransfer = $this
            ->checkoutClient
            ->placeOrder($quoteTransfer);

        $this->hydrateResponseObject($responseObject, $checkoutResponseTransfer, $branch);
    }

    /**
     * @param stdClass $responseObject
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @param BranchTransfer $branchTransfer
     */
    protected function hydrateResponseObject(stdClass $responseObject, CheckoutResponseTransfer $checkoutResponseTransfer, BranchTransfer $branchTransfer)
    {
        $responseObject->{Response::KEY_ORDER_CONFIRMATION} = $checkoutResponseTransfer->getIsSuccess();
        $responseObject->{Response::KEY_ORDER_REFERENCE} = $this->getOrderRefFromResponse($checkoutResponseTransfer);

        $responseObject->{Response::KEY_CHECKOUT_ERRORS} = [];
        foreach ($checkoutResponseTransfer->getErrors() as $error) {
            $errorObject = new stdClass();
            $errorObject->{Response::KEY_CHECKOUT_ERROR_CODE} = $error->getErrorCode();
            $errorObject->{Response::KEY_CHECKOUT_ERROR_MESSAGE} = $error->getMessage();

            $responseObject->{Response::KEY_CHECKOUT_ERRORS}[] = $errorObject;
        }
        $responseObject->{Response::KEY_SHIPPING_MERCHANT} = $branchTransfer->getName();
        $responseObject->{Response::KEY_PAYMENT_PENDING} = $checkoutResponseTransfer->getIsPending();
        $responseObject->{Response::KEY_PAYMENT_REDIRECT_URL} = $checkoutResponseTransfer->getRedirectUrl();
        $responseObject->{Response::KEY_PAYMENT_RETURN_URL} = $checkoutResponseTransfer->getReturnUrl();
        $responseObject->{Response::KEY_PAYMENT_PAYMENT_ID} = $checkoutResponseTransfer->getPaymentId();
    }

    /**
     * @param stdClass $requestObject
     * @return BranchTransfer
     */
    protected function getBranch(stdClass $requestObject)
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($requestObject->{Request::KEY_ID_BRANCH});

        return $this
            ->client
            ->getBranchById($requestTransfer);
    }

    /**
     * @param stdClass $requestObject
     * @return array
     */
    protected function getItems(stdClass $requestObject)
    {
        $itemTransfers = [];
        foreach($requestObject->{Request::KEY_ITEMS} as $item){
            $itemTransfer = new ItemTransfer();
            $itemTransfer->setSku($item->{Request::KEY_ITEM_SKU});
            $itemTransfer->setQuantity($item->{Request::KEY_ITEM_QUANTITY});
            $itemTransfers[] = $itemTransfer;
        }

        return $itemTransfers;
    }

    /**
     * @param stdClass $requestObject
     * @return ConcreteTimeSlotTransfer|null
     */
    protected function getConcreteTimeSlot(stdClass $requestObject)
    {
        if(!isset($requestObject->{Request::KEY_ID_CONCRETE_TIME_SLOT})){
            return null;
        }

        return $this
            ->deliveryAreaClient
            ->getConcreteTimeSlotById($requestObject->{Request::KEY_ID_CONCRETE_TIME_SLOT});
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param stdClass $requestObject
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, stdClass $requestObject)
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrateQuote($quoteTransfer, $requestObject);
        }
    }

    /**
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return string|null
     */
    protected function getOrderRefFromResponse(CheckoutResponseTransfer $checkoutResponseTransfer) : ?string
    {
        if($checkoutResponseTransfer->getSaveOrder() !== null){
            return $checkoutResponseTransfer->getSaveOrder()->getOrderReference();
        }

        return null;
    }

    /**
     * @param BranchTransfer $branch
     * @param stdClass $requestObject
     * @return bool
     */
    protected function useFlexibleTimeSlots(BranchTransfer $branch, stdClass $requestObject) : bool
    {
        if(
            $branch->getUsesGraphmasters() &&
            (
                isset($requestObject->{Request::KEY_ID_TIME_SLOT_START}) &&
                isset($requestObject->{Request::KEY_ID_TIME_SLOT_END})
            )
        )
        {
            return true;
        }

        return false;
    }
}
