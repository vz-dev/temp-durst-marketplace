<?php
/**
 * Durst - project - BillingPeriodInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.05.20
 * Time: 09:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData;


use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Resources\Metadata;

interface BillingPeriodInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return Metadata|null
     */
    public function getBillingPeriodMetaData(OrderTransfer $orderTransfer): ?Metadata;
}
