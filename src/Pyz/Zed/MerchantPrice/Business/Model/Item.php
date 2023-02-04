<?php

namespace Pyz\Zed\MerchantPrice\Business\Model;

use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\MerchantPrice\MerchantPriceConfig;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;

class Item
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var MerchantPriceConfig
     */
    protected $config;

    /**
     * Price constructor.
     *
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Product\Business\ProductFacadeInterface $productFacade
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        ProductFacadeInterface $productFacade,
        MerchantPriceConfig $config
    ) {
        $this->queryContainer = $queryContainer;
        $this->productFacade = $productFacade;
        $this->config = $config;
    }

    /**
     * function to prepare the relevance and the sorting key for the products that are sold.
     */
    public function countSoldItems() {
        $merchantPrices = $this->queryContainer
            ->queryPrices()
            ->find()
            ->getData();

        foreach($merchantPrices as $price) {
            $orderSales = $this->queryContainer
                ->querySalesOrdersByIdBranchAndIdProduct(
                    $price->getFkBranch(),
                    $price->getFkProduct(),
                    $this->config->getCountSoldItemsPeriod()
                );
            $price->setCountSoldItems($orderSales);
            $price->save();
        }

        $arrayToSort = $this->queryContainer
            ->queryPrices()
            ->find()
            ->toArray();

        $countArray = [];
        foreach ($arrayToSort as $key => $row) {
            $countArray[$key] = $row['CountSoldItems'];
        }
        array_multisort($countArray, SORT_DESC, $arrayToSort);

        foreach ($merchantPrices as $price) {
            foreach ($arrayToSort as $key => $value) {
                if($price->getIdPrice() == $value['IdPrice']) {
                    $price->setSortItems($key);
                    $price->save();
                }
            }
        }
    }
}
