<?php
/**
 * Durst - project - DeliveryAreaFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 11:30
 */

namespace Pyz\Client\DeliveryArea;


use Pyz\Client\DeliveryArea\Zed\DeliveryAreaStub;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DeliveryAreaFactory extends AbstractFactory
{
    /**
     * @return DeliveryAreaStub
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDeliveryAreaStub() : DeliveryAreaStub
    {
        return new DeliveryAreaStub(
            $this->getZedService()
        );
    }

    /**
     * @return ZedRequestClientInterface
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getZedService() : ZedRequestClientInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::SERVICE_ZED);
    }
}
