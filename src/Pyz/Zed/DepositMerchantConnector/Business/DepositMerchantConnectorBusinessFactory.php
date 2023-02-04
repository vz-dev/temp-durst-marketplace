<?php
/**
 * Durst - project - DepositMerchantConnectorBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:29
 */

namespace Pyz\Zed\DepositMerchantConnector\Business;

use Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranch;
use Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranchInterface;
use Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridgeInterface;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridgeInterface;
use Pyz\Zed\DepositMerchantConnector\DepositMerchantConnectorDependencyProvider;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

class DepositMerchantConnectorBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranchInterface
     */
    public function createDepositBranchModel(): DepositBranchInterface
    {
        return new DepositBranch(
            $this->getDepositQueryContainer(),
            $this->getMerchantPriceQueryContainer(),
            $this->getLocaleFacade(),
            $this->getTaxFacade()
        );
    }

    /**
     *
     * @return \Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridgeInterface
     */
    protected function getDepositQueryContainer(): DepositMerchantConnectorToDepositBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DepositMerchantConnectorDependencyProvider::QUERY_CONTAINER_DEPOSIT
            );
    }

    /**
     * @return \Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface
     */
    protected function getLocaleFacade(): DepositMerchantConnectorToLocaleBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DepositMerchantConnectorDependencyProvider::FACADE_LOCALE
            );
    }

    protected function getMerchantPriceQueryContainer(): DepositMerchantConnectorToMerchantPriceBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DepositMerchantConnectorDependencyProvider::QUERY_CONTAINER_MERCHANT_PRICE
            );
    }

    /**
     * @return TaxFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTaxFacade() : TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DepositMerchantConnectorDependencyProvider::FACADE_TAX
            );
    }
}
