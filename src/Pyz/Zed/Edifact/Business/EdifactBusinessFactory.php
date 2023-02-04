<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:12
 */

namespace Pyz\Zed\Edifact\Business;


use Pyz\Zed\Edifact\Business\Config\EdifactExportVersionConfig;
use Pyz\Zed\Edifact\Business\Log\Logger;
use Pyz\Zed\Edifact\Business\Log\LoggerInterface;
use Pyz\Zed\Edifact\EdifactDependencyProvider;
use Pyz\Zed\Edifact\Persistence\EdifactQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class EdifactBusinessFactory
 * @package Pyz\Zed\Edifact\Business
 * @method EdifactQueryContainerInterface getQueryContainer()
 */
class EdifactBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return TourFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getTourFacade(): TourFacadeInterface
    {
        return $this
            ->getProvidedDependency(EdifactDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return LoggerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createEdifactExportLogger(): LoggerInterface
    {
        return new Logger(
            $this->getQueryContainer(),
            $this->getTourFacade(),
            $this->getGraphMastersFacade()
        );
    }

    /**
     * @return EdifactExportVersionConfig
     * @throws ContainerKeyNotFoundException
     */
    public function getEdifactExportVersionConfig(): EdifactExportVersionConfig
    {
        return EdifactExportVersionConfig::getInstance(
            $this->getTourFacade(),
            $this->getMerchantFacade(),
            $this->getGraphMastersFacade()
        );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(EdifactDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(EdifactDependencyProvider::FACADE_GRAPHMASTERS);
    }
}
