<?php
/**
 * Durst - project - HeidelpayRestZedStubInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 12:02
 */

namespace Pyz\Client\HeidelpayRest\Zed;

use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;

interface HeidelpayRestZedStubInterface
{
    /**
     * @param string $idPayment
     * @return string
     */
    public function getAuthorizationStatusByPaymentId(string $idPayment): HeidelpayRestAuthorizationTransfer;

    /**
     * @param string $orderRef
     * @return HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer;
}
