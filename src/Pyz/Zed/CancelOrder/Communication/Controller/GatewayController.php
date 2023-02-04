<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 13:40
 */

namespace Pyz\Zed\CancelOrder\Communication\Controller;

use DateTime;
use Exception;
use Generated\Shared\Transfer\CancelOrderApiRequestTransfer;
use Generated\Shared\Transfer\CancelOrderApiResponseTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer;
use Generated\Shared\Transfer\DurstCompanyTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\StartCancel;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\CancelOrder\Communication\Controller
 *
 * @method CancelOrderCommunicationFactory getFactory()
 * @method CancelOrderFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     */
    public function cancelOrderDriverAction(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        $response = new CancelOrderApiResponseTransfer();

        try {
            $this
                ->updateSalesOrderForDriver(
                    $cancelOrderApiRequestTransfer
                );

            $transfer = $this
                ->getFacade()
                ->getJwtTransferForDriver(
                    $cancelOrderApiRequestTransfer
                        ->getOrderId(),
                    new DateTime('tomorrow midnight')
                );

            $transfer = $this
                ->getFacade()
                ->prepareTriggerFromToken(
                    $transfer
                        ->getToken()
                );

            $salesOrderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder(
                    $transfer
                        ->getId()
                );

            $idSalesOrderItems = $this
                ->getSalesOrderItemIds(
                    $salesOrderTransfer
                );

            $this
                ->getFactory()
                ->getOmsFacade()
                ->triggerEventForOrderItems(
                    StartCancel::EVENT_ID,
                    $idSalesOrderItems
                );

        } catch (Exception $exception) {
            $response
                ->setErrorMessage(
                    $exception
                        ->getMessage()
                );
        }

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     */
    public function isCanceledAction(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        $cancelOrder = $this
            ->getFacade()
            ->getCancelOrderByIdSalesOrder(
                $cancelOrderApiRequestTransfer
                    ->getOrderId()
            );

        return (new CancelOrderApiResponseTransfer())
            ->setAlreadyCanceled(
                $cancelOrder !== null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    public function cancelOrderCustomerAction(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        $response = new CancelOrderCustomerResponseTransfer();

        try {
            $transfer = $this
                ->getFacade()
                ->getJwtFromToken(
                    $cancelOrderCustomerRequestTransfer
                        ->getToken()
                );

            if ($transfer->getErrors()->count() > 0) {
                throw new Exception(
                    $transfer
                        ->getErrors()
                        ->offsetGet(0)
                        ->getMessage()
                );
            }

            $transfer = $this
                ->getFacade()
                ->executeJwtValidators(
                    $transfer
                );

            if ($transfer->getErrors()->count() > 0) {
                throw new Exception(
                    $transfer
                        ->getErrors()
                        ->offsetGet(0)
                        ->getMessage()
                );
            }

            $this
                ->updateSalesOrderIssuerForCustomer(
                    $transfer
                        ->getId()
                );

            $transfer = $this
                ->getFacade()
                ->prepareTriggerFromToken(
                    $transfer
                        ->getToken()
                );

            $salesOrderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder(
                    $transfer
                        ->getId()
                );

            $idSalesOrderItems = $this
                ->getSalesOrderItemIds(
                    $salesOrderTransfer
                );

            $this
                ->getFactory()
                ->getOmsFacade()
                ->triggerEventForOrderItems(
                    StartCancel::EVENT_ID,
                    $idSalesOrderItems
                );
        } catch (Exception $exception) {
            $response
                ->setErrorMessage(
                    $exception
                        ->getMessage()
                );
        }

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function parseTokenAction(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        $config = $this
            ->getFactory()
            ->getConfig();

        $response = (new CancelOrderCustomerResponseTransfer())
            ->setDurst(
                $this
                    ->getDurstFooterInformation()
            )
            ->setBaseUrl(
                $config
                    ->getBaseUrl()
            )
            ->setFooterBannerAlt(
                $config
                    ->getFooterBannerAlt()
            )
            ->setFooterBannerCta(
                $config
                    ->getFooterBannerCta()
            )
            ->setFooterBannerImg(
                $config
                    ->getFooterBannerImg()
            )
            ->setFooterBannerLink(
                $config
                    ->getFooterBannerLink()
            );

        try {
            $jwt = $this
                ->getFactory()
                ->getCancelOrderFacade()
                ->prepareTriggerFromToken(
                    $cancelOrderCustomerRequestTransfer
                        ->getToken()
                );

            $salesOrder = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder(
                    $jwt
                        ->getId()
                );

            $response
                ->setJwt(
                    $jwt
                )
                ->setSalesOrder(
                    $salesOrder
                );

        } catch (Exception $exception) {
            $response
                ->setErrorMessage(
                    $exception
                        ->getMessage()
                );
        }

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    public function verifySignerAction(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        $response = new CancelOrderCustomerResponseTransfer();

        $errorMessage = null;

        try {
            $success = $this
                ->getFacade()
                ->verifySignByToken(
                    $cancelOrderCustomerRequestTransfer
                        ->getToken(),
                    $cancelOrderCustomerRequestTransfer
                        ->getSigner()
                );

            if ($success !== true) {
                $errorMessage = 'cancel-order-error';
            }

        } catch (Exception $exception) {
            $errorMessage = $exception
                ->getMessage();
        }

        return $response
            ->setErrorMessage(
                $errorMessage
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function updateSalesOrderForDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): void
    {
        $this
            ->updateSalesOrderDriverForDriver(
                $cancelOrderApiRequestTransfer
            );

        $this
            ->updateSalesOrderIssuerForDriver(
                $cancelOrderApiRequestTransfer
            );

        $this
            ->updateSalesOrderCancelMessageForDriver(
                $cancelOrderApiRequestTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function updateSalesOrderDriverForDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): void
    {
        $driver = $this
            ->getFactory()
            ->getAuthFacade()
            ->getDriverByToken(
                $cancelOrderApiRequestTransfer
                    ->getToken()
            );

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateDriverForOrder(
                $cancelOrderApiRequestTransfer
                    ->getOrderId(),
                $driver
                    ->getIdDriver()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function updateSalesOrderIssuerForDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): void
    {
        $config = $this
            ->getFactory()
            ->getConfig();

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateCancelIssuer(
                $cancelOrderApiRequestTransfer
                    ->getOrderId(),
                $config
                    ->getIssuerDriver()
            );
    }

    /**
     * @param int $idSalesOrder
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function updateSalesOrderIssuerForCustomer(
        int $idSalesOrder
    ): void
    {
        $config = $this
            ->getFactory()
            ->getConfig();

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateCancelIssuer(
                $idSalesOrder,
                $config
                    ->getIssuerCustomer()
            );

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateCancelMessage(
                $idSalesOrder,
                null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function updateSalesOrderCancelMessageForDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): void
    {
        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateCancelMessage(
                $cancelOrderApiRequestTransfer
                    ->getOrderId(),
                $cancelOrderApiRequestTransfer
                    ->getCancelMessage()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @return array
     */
    protected function getSalesOrderItemIds(
        OrderTransfer $order
    ): array
    {
        $ids = [];

        foreach ($order->getItems() as $item) {
            $ids[] = $item
                ->getIdSalesOrderItem();
        }

        return $ids;
    }

    /**
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDurstFooterInformation(): DurstCompanyTransfer
    {
        return $this
            ->getFactory()
            ->getOmsFacade()
            ->createDurstCompanyTransfer();
    }
}
