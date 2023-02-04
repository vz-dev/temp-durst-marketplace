<?php

namespace Pyz\Zed\Deposit\Persistence;

use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Pyz\Zed\Deposit\DepositDependencyProvider;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;


class DepositPersistenceFactory extends AbstractPersistenceFactory
{

    /**
     * @return SpyDepositQuery
     */
    public function createDepositQuery()
    {
        return new SpyDepositQuery();
    }

    /**
     * @return \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    public function getProductQueryContainer()
    {
        return $this->getProvidedDependency(DepositDependencyProvider::QUERY_CONTAINER_PRODUCT);
    }
}
