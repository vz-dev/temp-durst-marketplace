<?php
/**
 * Durst - project - SoapRequestDependencyProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-30
 * Time: 17:20
 */

namespace Pyz\Zed\SoapRequest;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class SoapRequestDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';

    public const SERVICE_SOAP_REQUEST = 'SERVICE_SOAP_REQUEST';

    /**
     * @param Container $container
     * @return Container|void
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addServiceSoapRequest($container);
        $container = $this->addFacadeIntegra($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addServiceSoapRequest(Container $container){
        $container[static::SERVICE_SOAP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->soapRequest()
                ->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addFacadeIntegra(Container $container){
        $container[static::FACADE_INTEGRA] = function (Container $container) {
            return $container
                ->getLocator()
                ->integra()
                ->facade();
        };

        return $container;
    }

}
