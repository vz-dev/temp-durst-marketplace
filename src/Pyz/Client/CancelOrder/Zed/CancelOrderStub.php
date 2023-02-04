<?php
/**
 * Durst - project - CancelOrderStub.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:27
 */

namespace Pyz\Client\CancelOrder\Zed;

use Generated\Shared\Transfer\CancelOrderApiRequestTransfer;
use Generated\Shared\Transfer\CancelOrderApiResponseTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class CancelOrderStub implements CancelOrderStubInterface
{
    protected const URL_CANCEL_ORDER_BY_DRIVER = '/cancel-order/gateway/cancel-order-driver';
    protected const URL_IS_ORDER_CANCELED = '/cancel-order/gateway/is-canceled';

    protected const URL_CANCEL_ORDER_BY_CUSTOMER = '/cancel-order/gateway/cancel-order-customer';
    protected const URL_PARSE_TOKEN = '/cancel-order/gateway/parse-token';
    protected const URL_VERIFY_SIGNER = '/cancel-order/gateway/verify-signer';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(
        ZedRequestClientInterface $zedStub
    )
    {
        $this->zedStub = $zedStub;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function cancelOrderByDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_CANCEL_ORDER_BY_DRIVER,
                $cancelOrderApiRequestTransfer,
                null
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function isOrderCanceled(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_IS_ORDER_CANCELED,
                $cancelOrderApiRequestTransfer,
                null
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function cancelOrderByCustomer(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_CANCEL_ORDER_BY_CUSTOMER,
                $cancelOrderCustomerRequestTransfer,
                null
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function parseToken(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_PARSE_TOKEN,
                $cancelOrderCustomerRequestTransfer,
                null
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function verifySigner(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                static::URL_VERIFY_SIGNER,
                $cancelOrderCustomerRequestTransfer,
                null
            );
    }
}
