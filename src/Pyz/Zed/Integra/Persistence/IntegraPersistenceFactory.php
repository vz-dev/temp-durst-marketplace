<?php

namespace Pyz\Zed\Integra\Persistence;

use Orm\Zed\Integra\Persistence\PyzIntegraCredentialsQuery;
use Orm\Zed\Integra\Persistence\PyzIntegraLogQuery;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\Integra\IntegraDependencyProvider;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Pyz\Zed\Refund\Persistence\RefundQueryContainerInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\Integra\IntegraConfig getConfig()
 * @method \Pyz\Zed\Integra\Persistence\IntegraQueryContainer getQueryContainer()
 */
class IntegraPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    public function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_SALES);
    }

    /**
     * @return \Orm\Zed\Integra\Persistence\PyzIntegraCredentialsQuery
     */
    public function createIntegraCredentialsQuery(): PyzIntegraCredentialsQuery
    {
        return PyzIntegraCredentialsQuery::create();
    }

    /**
     * @return \Orm\Zed\Integra\Persistence\PyzIntegraLogQuery
     */
    public function createIntegraLogQuery(): PyzIntegraLogQuery
    {
        return PyzIntegraLogQuery::create();
    }

    /**
     * @return \Pyz\Zed\Refund\Persistence\RefundQueryContainerInterface
     */
    public function getRefundQueryContainer(): RefundQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_REFUND);
    }

    /**
     * @return \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface
     */
    public function getDeliveryAreaQueryContainer(): DeliveryAreaQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_DELIVERY_AREA);
    }

    /**
     * @return \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    public function getTourQueryContainer(): TourQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_TOUR);
    }

    /**
     * @return \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    public function getMerchantPriceQueryContainer(): MerchantPriceQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_MERCHANT_PRICE);
    }

    /**
     * @return \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface
     */
    public function getDepositQueryContainer(): DepositQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_DEPOSIT);
    }
}
