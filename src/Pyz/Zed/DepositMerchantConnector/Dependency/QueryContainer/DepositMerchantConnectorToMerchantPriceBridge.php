<?php
/**
 * Durst - project - DepositMerchantConnectorToMerchantPriceBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 14:37
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer;

use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;

class DepositMerchantConnectorToMerchantPriceBridge implements DepositMerchantConnectorToMerchantPriceBridgeInterface
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $merchantPriceQueryContainer;

    /**
     * DepositMerchantConnectorToMerchantPriceBridge constructor.
     *
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $merchantPriceQueryContainer
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $merchantPriceQueryContainer
    ) {
        $this->merchantPriceQueryContainer = $merchantPriceQueryContainer;
    }

    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPricesByIdBranch(int $idBranch): MerchantPriceQuery
    {
        return $this
            ->merchantPriceQueryContainer
            ->queryPricesByIdBranch($idBranch);
    }
}
