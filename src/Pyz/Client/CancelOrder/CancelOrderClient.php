<?php
/**
 * Durst - project - CancelOrderClient.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:37
 */

namespace Pyz\Client\CancelOrder;

use Generated\Shared\Transfer\CancelOrderApiRequestTransfer;
use Generated\Shared\Transfer\CancelOrderApiResponseTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class CancelOrderClient
 * @package Pyz\Client\CancelOrder
 *
 * @method CancelOrderFactory getFactory()
 */
class CancelOrderClient extends AbstractClient implements CancelOrderClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function cancelOrderByDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderStub()
            ->cancelOrderByDriver(
                $cancelOrderApiRequestTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isOrderCanceled(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderStub()
            ->isOrderCanceled(
                $cancelOrderApiRequestTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function cancelOrderByCustomer(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderStub()
            ->cancelOrderByCustomer(
                $cancelOrderCustomerRequestTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function parseToken(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderStub()
            ->parseToken(
                $cancelOrderCustomerRequestTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function verifySigner(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderStub()
            ->verifySigner(
                $cancelOrderCustomerRequestTransfer
            );
    }
}
