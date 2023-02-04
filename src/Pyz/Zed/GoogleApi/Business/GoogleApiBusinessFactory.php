<?php
/**
 * Durst - project - GoogleApiBusinessFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 07:59
 */

namespace Pyz\Zed\GoogleApi\Business;


use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\GoogleApi\Business\Checkout\LatLngOrderAddressSaver;
use Pyz\Zed\GoogleApi\Business\Checkout\LatLngOrderAddressSaverInterface;
use Pyz\Zed\GoogleApi\Business\Geocoder\Geocoder;
use Pyz\Zed\GoogleApi\Business\Geocoder\GeocoderInterface;
use Pyz\Zed\GoogleApi\Dependency\GoogleApiToHttpRequestBridgeInterface;
use Pyz\Zed\GoogleApi\GoogleApiConfig;
use Pyz\Zed\GoogleApi\GoogleApiDependencyProvider;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class GoogleApiBusinessFactory
 * @package Pyz\Zed\GoogleApi\Business
 * @method GoogleApiConfig getConfig()
 */
class GoogleApiBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getHttpRequestFacade(): GoogleApiToHttpRequestBridgeInterface
    {
        return $this
            ->getProvidedDependency(GoogleApiDependencyProvider::FACADE_HTTP_REQUEST);
    }

    /**
     * @return \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getHttpRequestService(): HttpRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(GoogleApiDependencyProvider::SERVICE_HTTP_REQUEST);
    }

    /**
     * @return GeocoderInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createGeocoder(): GeocoderInterface
    {
        return new Geocoder(
            $this->getHttpRequestService(),
            $this->getHttpRequestFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return LatLngOrderAddressSaver
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createLatLngAddressSaver(): LatLngOrderAddressSaverInterface
    {
        return new LatLngOrderAddressSaver(
            $this->getSalesQueryContainer(),
            $this->createGeocoder()
        );
    }

    /**
     * @return SalesFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(GoogleApiDependencyProvider::FACADE_SALES);
    }

    /**
     * @return SalesQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(GoogleApiDependencyProvider::QUERY_CONTAINER_SALES);
    }
}
