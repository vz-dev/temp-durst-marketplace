<?php
/**
 * Durst - project - AppRestApiCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 14:02
 */

namespace Pyz\Zed\AppRestApi\Communication;


use Pyz\Zed\AppRestApi\AppRestApiDependencyProvider;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

class AppRestApiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return DeliveryAreaFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDeliveryAreaFacade() : DeliveryAreaFacadeInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return MerchantPriceFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantPriceFacade() : MerchantPriceFacadeInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::FACADE_MERCHANT_PRICE);
    }
}