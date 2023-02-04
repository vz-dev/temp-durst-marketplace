<?php
/**
 * Durst - project - CancelOrderClientInterface.php.
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

interface CancelOrderClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     */
    public function cancelOrderByDriver(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderApiResponseTransfer
     */
    public function isOrderCanceled(
        CancelOrderApiRequestTransfer $cancelOrderApiRequestTransfer
    ): CancelOrderApiResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    public function cancelOrderByCustomer(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    public function parseToken(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    public function verifySigner(
        CancelOrderCustomerRequestTransfer $cancelOrderCustomerRequestTransfer
    ): CancelOrderCustomerResponseTransfer;
}
