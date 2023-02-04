<?php
/**
 * Durst - project - DiscountFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 14:45
 */

namespace Pyz\Client\Discount;


use Pyz\Client\Discount\Zed\DiscountStub;
use Pyz\Client\Discount\Zed\DiscountStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DiscountFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\Discount\Zed\DiscountStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDiscountStub(): DiscountStubInterface
    {
        return new DiscountStub(
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
            ->getProvidedDependency(
                DiscountDependencyProvider::SERVICE_ZED
            );
    }
}
