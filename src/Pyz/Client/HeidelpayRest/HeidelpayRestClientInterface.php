<?php
/**
 * Durst - project - HeidelpayRestClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.01.19
 * Time: 15:07
 */

namespace Pyz\Client\HeidelpayRest;

use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;

interface HeidelpayRestClientInterface
{
    /**
     * @param string $idPayment
     * @return string
     */
    public function getAuthorizationStatusByPaymentId(string $idPayment): HeidelpayRestAuthorizationTransfer;

    /**
     * @param string $idPayment
     * @return string
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer;
}
