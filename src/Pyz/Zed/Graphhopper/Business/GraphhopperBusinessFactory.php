<?php
/**
 * Durst - project - GraphhopperBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 15:42
 */

namespace Pyz\Zed\Graphhopper\Business;


use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\Graphhopper\Business\Checkout\LatLngOrderAddressSaver;
use Pyz\Zed\Graphhopper\Business\Checkout\LatLngOrderAddressSaverInterface;
use Pyz\Zed\Graphhopper\Business\Model\Geocoding;
use Pyz\Zed\Graphhopper\Business\Model\GeocodingInterface;
use Pyz\Zed\Graphhopper\Business\Model\TourOrderSorter;
use Pyz\Zed\Graphhopper\Business\Model\TourOrderSorterInterface;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToTourBridgeInterface;
use Pyz\Zed\Graphhopper\GraphhopperConfig;
use Pyz\Zed\Graphhopper\GraphhopperDependencyProvider;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class GraphhopperBusinessFactory
 * @package Pyz\Zed\Graphhopper\Business
 * @method GraphhopperConfig getConfig()
 * @method GraphhopperQueryContanerInterface getQueryContainer()
 */
class GraphhopperBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getHttpRequestFacade(): GraphhopperToHttpRequestBridgeInterface
    {
        return $this
            ->getProvidedDependency(GraphhopperDependencyProvider::FACADE_HTTP_REQUEST);
    }

    /**
     * @return \Pyz\Zed\Graphhopper\Dependency\GraphhopperToTourBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTourFacade(): GraphhopperToTourBridgeInterface
    {
        return $this
            ->getProvidedDependency(GraphhopperDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getHttpRequestService(): HttpRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(GraphhopperDependencyProvider::SERVICE_HTTP_REQUEST);
    }

    /**
     * @return \Pyz\Zed\Graphhopper\Business\Model\GeocodingInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createGeocoding(): GeocodingInterface
    {
        return new Geocoding(
            $this->getHttpRequestService(),
            $this->getHttpRequestFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Graphhopper\Business\Model\TourOrderSorterInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createTourOrderSorter(): TourOrderSorterInterface
    {
        return new TourOrderSorter(
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
            $this->createGeocoding()
        );
    }

    /**
     * @return SalesFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesFacade() : SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(GraphhopperDependencyProvider::FACADE_SALES);
    }

    /**
     * @return SalesQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesQueryContainer() : SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(GraphhopperDependencyProvider::QUERY_CONTAINER_SALES);
    }
}
