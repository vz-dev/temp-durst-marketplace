<?php


namespace Pyz\Client\Deposit;


use Pyz\Client\Deposit\Zed\DepositStub;
use Pyz\Client\Deposit\Zed\DepositStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DepositFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Deposit\Zed\DepositStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDepositStub(): DepositStubInterface
    {
        return new DepositStub(
            $this->getZedService()
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClientInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getZedService(): ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(DepositDependencyProvider::SERVICE_ZED);
    }
}