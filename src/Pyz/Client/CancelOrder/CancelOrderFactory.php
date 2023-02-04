<?php
/**
 * Durst - project - CancelOrderFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 14:41
 */

namespace Pyz\Client\CancelOrder;

use Pyz\Client\CancelOrder\Zed\CancelOrderStub;
use Pyz\Client\CancelOrder\Zed\CancelOrderStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class CancelOrderFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Client\CancelOrder\Zed\CancelOrderStubInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCancelOrderStub(): CancelOrderStubInterface
    {
        return new CancelOrderStub(
            $this
                ->getZedService()
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
                CancelOrderDependencyProvider::SERVICE_ZED
            );
    }
}
