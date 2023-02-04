<?php


namespace Pyz\Zed\Deposit\Communication;


use Pyz\Zed\Deposit\DepositDependencyProvider;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

class DepositCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(DepositDependencyProvider::FACADE_MERCHANT);
    }
}
