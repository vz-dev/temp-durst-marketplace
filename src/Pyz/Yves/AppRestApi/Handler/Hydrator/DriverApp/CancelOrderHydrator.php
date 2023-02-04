<?php
/**
 * Durst - project - CancelOrderHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:24
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;

use Generated\Shared\Transfer\CancelOrderApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\CancelOrder\CancelOrderClientInterface;
use Pyz\Client\Sales\SalesClientInterface;
use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverCancelOrderRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverCancelOrderResponseInterface;
use stdClass;

class CancelOrderHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\Sales\SalesClientInterface
     */
    protected $salesClient;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * @var \Pyz\Client\CancelOrder\CancelOrderClientInterface
     */
    protected $cancelOrderClient;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Generated\Shared\Transfer\OrderTransfer
     */
    protected $orderTransfer;

    /**
     * @var \Generated\Shared\Transfer\DriverTransfer
     */
    protected $driver;

    /**
     * @param \Pyz\Client\Sales\SalesClientInterface $salesClient
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     * @param \Pyz\Client\CancelOrder\CancelOrderClientInterface $cancelOrderClient
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     */
    public function __construct(
        SalesClientInterface $salesClient,
        AuthClientInterface $authClient,
        CancelOrderClientInterface $cancelOrderClient,
        AppRestApiConfig $config
    )
    {
        $this->salesClient = $salesClient;
        $this->authClient = $authClient;
        $this->cancelOrderClient = $cancelOrderClient;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @param string $version
     * @return void
     */
    public function hydrate(
        stdClass $requestObject,
        stdClass $responseObject,
        string $version = 'v1'
    ): void
    {
        $authenticated = $this
            ->authenticateDriver(
                $requestObject
            );

        $responseObject->{DriverCancelOrderResponseInterface::KEY_AUTH_VALID} = $authenticated;
        $responseObject->{DriverCancelOrderResponseInterface::KEY_ALREADY_CANCELED} = false;

        $checkOrder = $this
            ->checkOrder(
                $requestObject
            );

        if ($authenticated !== true || $checkOrder !== true) {
            $responseObject->{DriverCancelOrderResponseInterface::KEY_ORDER_CANCELED} = false;
            return;
        }

        if (
            $this->assertAllItemsAreReadyForDelivery() !== true ||
            $this->isOrderAlreadyCanceled($requestObject) === true
        ) {
            $responseObject->{DriverCancelOrderResponseInterface::KEY_ORDER_CANCELED} = false;
            $responseObject->{DriverCancelOrderResponseInterface::KEY_ALREADY_CANCELED} = true;
            return;
        }

        $responseObject->{DriverCancelOrderResponseInterface::KEY_ORDER_CANCELED} = true;

        $cancelOrderTransfer = $this
            ->prepareData(
                $requestObject
            );

        $response = $this
            ->cancelOrderClient
            ->cancelOrderByDriver(
                $cancelOrderTransfer
            );

        if ($response->getErrorMessage() !== null) {
            $responseObject->{DriverCancelOrderResponseInterface::KEY_ORDER_CANCELED} = false;
            $responseObject->{DriverCancelOrderResponseInterface::KEY_ERROR_MESSAGE} = $response->getErrorMessage();
        }
    }



    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function authenticateDriver(stdClass $requestObject): bool
    {
        $token = $requestObject
            ->{DriverCancelOrderRequestInterface::KEY_TOKEN};

        if ($token == null || trim($token) == '') {
            return false;
        }

        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken(
                $this
                    ->getToken(
                        $requestObject
                    )
            );

        $response = $this
            ->authClient
            ->authenticateDriver($requestTransfer);

        return $response
            ->getAuthValid();
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getToken(
        stdClass $requestObject
    ): string
    {
        return $requestObject
            ->{DriverCancelOrderRequestInterface::KEY_TOKEN};
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
     * @return bool
     */
    protected function checkOrder(stdClass $requestObject): bool
    {
        $this->driver = $this
            ->getDriverByToken(
                $this
                    ->getToken(
                        $requestObject
                    )
            );

        $idOrder = $requestObject->{DriverCancelOrderRequestInterface::KEY_ORDER_ID};

        $this->orderTransfer = $this
            ->salesClient
            ->getOrderByIdSalesOrder(
                $idOrder
            );

        return ($this->orderTransfer
                ->getBranch()
                ->getIdBranch() === $this->driver->getFkBranch());
    }

    /**
     * @param \stdClass $requestObject
     * @return bool
     */
    protected function isOrderAlreadyCanceled(stdClass $requestObject): bool
    {
        $requestTransfer = (new CancelOrderApiRequestTransfer())
            ->setToken(
                $this
                    ->getToken(
                        $requestObject
                    )
            )
            ->setOrderId(
                $requestObject->{DriverCancelOrderRequestInterface::KEY_ORDER_ID}
            );

        $response = $this
            ->cancelOrderClient
            ->isOrderCanceled(
                $requestTransfer
            );

        return $response
            ->getAlreadyCanceled();
    }

    /**
     * @return bool
     */
    protected function assertAllItemsAreReadyForDelivery() : bool
    {
        foreach ($this->orderTransfer->getItems() as $orderItem){
            if ($orderItem->getState()->getName() !== $this->config->getOmsStateReadyForDelivery())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\CancelOrderApiRequestTransfer
     */
    protected function prepareData(stdClass $requestObject): CancelOrderApiRequestTransfer
    {
        $transfer = new CancelOrderApiRequestTransfer();

        $transfer
            ->setOrderId(
                $requestObject
                    ->{DriverCancelOrderRequestInterface::KEY_ORDER_ID}
            )
            ->setToken(
                $requestObject
                    ->{DriverCancelOrderRequestInterface::KEY_TOKEN}
            )
            ->setCancelMessage(
                $this
                    ->getCancelMessage(
                        $requestObject
                    )
            );

        return $transfer;
    }

    /**
     * @param \stdClass $requestObject
     * @return string|null
     */
    protected function getCancelMessage(
        stdClass $requestObject
    ): ?string
    {
        switch ($requestObject->{DriverCancelOrderRequestInterface::KEY_CANCEL_MESSAGE}) {
            case 1:
                return CancelOrderConstants::CANCEL_MESSAGE_NOT_AT_HOME;
            case 2:
                return CancelOrderConstants::CANCEL_MESSAGE_NOT_ACCEPTED;
            default:
                return null;
        }
    }
}
