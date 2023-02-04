<?php
/**
 * Durst - project - DepositMerchantConnectorToDepositBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 14:37
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer;

use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;

class DepositMerchantConnectorToDepositBridge implements DepositMerchantConnectorToDepositBridgeInterface
{
    /**
     * @var \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface
     */
    protected $depositQueryContainer;

    /**
     * DepositMerchantConnectorToDepositBridge constructor.
     *
     * @param \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface $depositQueryContainer
     */
    public function __construct(DepositQueryContainerInterface $depositQueryContainer)
    {
        $this->depositQueryContainer = $depositQueryContainer;
    }

    /**
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDeposit(): SpyDepositQuery
    {
        return $this
            ->depositQueryContainer
            ->queryDeposit();
    }
}
