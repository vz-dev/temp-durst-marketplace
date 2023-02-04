<?php
/**
 * Durst - project - BillingToMoneyBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 14:13
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\MoneyTransfer;

interface BillingToMoneyBridgeInterface
{
    /**
     * @param int $amount
     * @param string|null $isoCode
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    public function fromInteger(
        int $amount,
        string $isoCode = null
    ): MoneyTransfer;

    /**
     * @param \Generated\Shared\Transfer\MoneyTransfer $moneyTransfer
     * @return string
     */
    public function formatWithSymbol(
        MoneyTransfer $moneyTransfer
    ): string;
}
