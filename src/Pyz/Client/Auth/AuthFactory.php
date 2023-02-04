<?php


namespace Pyz\Client\Auth;


use Pyz\Client\Auth\Zed\AuthStub;
use Pyz\Client\Auth\Zed\AuthStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class AuthFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Auth\Zed\AuthStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAuthStub(): AuthStubInterface
    {
        return new AuthStub(
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
            ->getProvidedDependency(AuthDependencyProvider::SERVICE_ZED);
    }
}