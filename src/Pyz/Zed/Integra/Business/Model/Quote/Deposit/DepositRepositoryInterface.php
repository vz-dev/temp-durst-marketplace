<?php
/**
 * Durst - project - DepositRepositoryInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 17:14
 */

namespace Pyz\Zed\Integra\Business\Model\Quote\Deposit;


use Orm\Zed\Deposit\Persistence\SpyDeposit;

interface DepositRepositoryInterface
{
    /**
     * @param string $sku
     *
     * @return \Orm\Zed\Deposit\Persistence\SpyDeposit
     */
    public function getDepositForSku(string $sku): SpyDeposit;

    /**
     * @param array $skus
     *
     * @return void
     */
    public function loadDeposits(
        array $skus
    ): void;
}
