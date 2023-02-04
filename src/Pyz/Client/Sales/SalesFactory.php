<?php
/**
 * Durst - project - SalesFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.09.18
 * Time: 14:53
 */

namespace Pyz\Client\Sales;

use Pyz\Client\Sales\Zed\SalesStub;

use Spryker\Client\Sales\SalesDependencyProvider;
use Spryker\Client\Sales\SalesFactory as SprykerSalesFactory;


class SalesFactory extends SprykerSalesFactory
{
    /**
     * @return SalesStub
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSalesStub()
    {
        return new SalesStub(
            $this->getProvidedDependency(SalesDependencyProvider::SERVICE_ZED)
        );
    }

}