<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:19
 */

namespace Pyz\Client\ProductGtin;

use Pyz\Client\ProductGtin\Zed\ProductGtinStub;
use Pyz\Client\ProductGtin\Zed\ProductGtinStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class ProductGtinFactory extends AbstractFactory
{
    /**
     * @return ProductGtinStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductGtinStub(): ProductGtinStubInterface
    {
        return new ProductGtinStub(
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
            ->getProvidedDependency(ProductGtinDependencyProvider::SERVICE_ZED);
    }

}
