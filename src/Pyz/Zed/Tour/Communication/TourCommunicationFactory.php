<?php

namespace Pyz\Zed\Tour\Communication;

use Exception;
use Generated\Shared\Transfer\DrivingLicenceTransfer;
use Pyz\Zed\Auth\Business\AuthFacadeInterface;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Graphhopper\Business\GraphhopperFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainer;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Business\Stream\DepositExportInputStream;
use Pyz\Zed\Tour\Business\Stream\DepositExportOutputStream;
use Pyz\Zed\Tour\Business\Stream\GraphMastersDepositExportInputStream;
use Pyz\Zed\Tour\Business\Stream\GraphMastersDepositExportOutputStream;
use Pyz\Zed\Tour\Business\Stream\GraphMastersTourExportInputStream;
use Pyz\Zed\Tour\Business\Stream\GraphMastersTourExportOutputStream;
use Pyz\Zed\Tour\Business\Stream\TourExportInputStream;
use Pyz\Zed\Tour\Business\Stream\TourExportOutputStream;
use Pyz\Zed\Tour\Business\TourFacade;
use Pyz\Zed\Tour\Communication\Form\DataProvider\DrivingLicenceFormDataProvider;
use Pyz\Zed\Tour\Communication\Form\DrivingLicenceCreateForm;
use Pyz\Zed\Tour\Communication\Form\DrivingLicenceEditForm;
use Pyz\Zed\Tour\Communication\Plugin\DepositExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\DepositExporter\DepositExportInputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\DepositExporter\DepositExportOutputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\DepositExporter\GraphMastersDepositExportInputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\DepositExporter\GraphMastersDepositExportOutputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\Edifact\EdifactExportVersionPlugin;
use Pyz\Zed\Tour\Communication\Plugin\GraphMastersDepositExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\GraphMastersTourExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExportConfigurationPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExporter\GraphMastersTourExportInputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExporter\GraphMastersTourExportOutputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExporter\TourExportInputStreamPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExporter\TourExportMapperPlugin;
use Pyz\Zed\Tour\Communication\Plugin\TourExporter\TourExportOutputStreamPlugin;
use Pyz\Zed\Tour\Communication\Table\DrivingLicenceTable;
use Pyz\Zed\Tour\Dependency\Facade\TourToMailBridgeInterface;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridgeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Pyz\Zed\Tour\TourDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\OmsFacadeInterface;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Communication\Plugin\Iterator\NullIteratorPlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\Log\MiddlewareLoggerConfigPlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\StreamReaderStagePlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\StreamWriterStagePlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PostProcessorHookPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Iterator\ProcessIteratorPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Log\MiddlewareLoggerConfigPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @method TourFacade getFacade()
 * @method TourQueryContainerInterface getQueryContainer()
 * @method TourConfig getConfig()
 */
class TourCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @param DrivingLicenceTransfer $data
     * @param array $options
     * @return FormInterface
     */
    public function createDrivingLicenceEditForm(DrivingLicenceTransfer $data, array $options = []) : FormInterface
    {
        return $this->getFormFactory()->create(DrivingLicenceEditForm::class, $data, $options);
    }

    /**
     * @param DrivingLicenceTransfer $data
     * @param array $options
     * @return FormInterface
     */
    public function createDrivingLicenceCreateForm(array $options = []) : FormInterface
    {
        $data = new DrivingLicenceTransfer;
        return $this->getFormFactory()->create(DrivingLicenceCreateForm::class, $data, $options);
    }

    /**
     * @return DrivingLicenceFormDataProvider
     */
    public function createDrivingLicenceDataProvider() : DrivingLicenceFormDataProvider
    {
        return new DrivingLicenceFormDataProvider($this->getFacade());
    }

    /**
     * @return DrivingLicenceTable
     */
    public function createDrivingLicenceTable() : DrivingLicenceTable
    {
        return new DrivingLicenceTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return OmsFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getOmsFacade(): OmsFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_OMS);
    }

    /**
     * @return OmsQueryContainer
     * @throws ContainerKeyNotFoundException
     */
    public function getOmsQueryContainer(): OmsQueryContainer
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_OMS);
    }

    /**
     * @return ProductQueryContainer
     * @throws ContainerKeyNotFoundException
     */
    public function getProductQueryContainer(): ProductQueryContainer
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_PRODUCT);
    }

    /**
     * @return array
     */
    public function getTourProcesses(): array
    {

        return [
            new TourExportConfigurationPlugin()
        ];
    }

    /**
     * @return array
     */
    public function getDepositProcesses(): array
    {
        return [
            new DepositExportConfigurationPlugin()
        ];
    }

    /**
     * @return array
     */
    public function getTourTranslatorFunctions(): array
    {

        return [];
    }

    /**
     * @return array
     */
    public function getDepositTranslatorFunctions(): array
    {
        return [];
    }

    /**
     * @return InputStreamPluginInterface
     */
    public function getTourExportInputStreamPlugin(): InputStreamPluginInterface
    {
        return new TourExportInputStreamPlugin();
    }

    /**
     * @return InputStreamPluginInterface
     */
    public function getDepositExportInputStreamPlugin(): InputStreamPluginInterface
    {
        return new DepositExportInputStreamPlugin();
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getTourExportOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new TourExportOutputStreamPlugin();
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getDepositExportOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new DepositExportOutputStreamPlugin();
    }

    /**
     * @return ProcessIteratorPluginInterface
     */
    public function getTourExportIteratorPlugin(): ProcessIteratorPluginInterface
    {
        return new NullIteratorPlugin();
    }

    /**
     * @return ProcessIteratorPluginInterface
     */
    public function getDepositExportIteratorPlugin(): ProcessIteratorPluginInterface
    {
        return new NullIteratorPlugin();
    }

    /**
     * @return StagePluginInterface[]
     */
    public function getTourExportStagePluginsStack(): array
    {
        return [
            new StreamReaderStagePlugin(),
            new TourExportMapperPlugin(),
            new StreamWriterStagePlugin()
        ];
    }

    /**
     * @return StagePluginInterface[]
     */
    public function getDepositExportStagePluginsStack(): array
    {
        return [
            new StreamReaderStagePlugin(),
            new TourExportMapperPlugin(),
            new StreamWriterStagePlugin()
        ];
    }

    /**
     * @return MiddlewareLoggerConfigPluginInterface
     */
    public function getTourLoggerConfigPlugin(): MiddlewareLoggerConfigPluginInterface
    {
        return new MiddlewareLoggerConfigPlugin();
    }

    /**
     * @return MiddlewareLoggerConfigPluginInterface
     */
    public function getDepositLoggerConfigPlugin(): MiddlewareLoggerConfigPluginInterface
    {
        return new MiddlewareLoggerConfigPlugin();
    }

    /**
     * @param bool $isGraphmastersTour
     * @return PreProcessorHookPluginInterface[]
     * @throws ContainerKeyNotFoundException
     */
    public function getTourExportPreProcessorPluginsStack(bool $isGraphmastersTour = false): array
    {
        return [
            new EdifactExportVersionPlugin($this->getEdifactFacade(), $isGraphmastersTour)
        ];
    }

    /**
     * @param bool $isGraphmastersTour
     * @return PreProcessorHookPluginInterface[]
     * @throws ContainerKeyNotFoundException
     */
    public function getDepositExportPreProcessorPluginsStack(bool $isGraphmastersTour = false): array
    {
        return [
            new EdifactExportVersionPlugin($this->getEdifactFacade(), $isGraphmastersTour)
        ];
    }

    /**
     * @return PostProcessorHookPluginInterface[]
     */
    public function getTourExportPostProcessorPluginsStack(): array
    {
        return [];
    }

    /**
     * @return PostProcessorHookPluginInterface[]
     */
    public function getDepositExportPostProcessorPluginsStack(): array
    {
        return [];
    }

    /**
     * @param int $idConcreteTour
     * @return ReadStreamInterface
     * @throws ContainerKeyNotFoundException
     * @throws Exception
     */
    public function createTourExportReadStream(int $idConcreteTour): ReadStreamInterface
    {
        return new TourExportInputStream(
            $idConcreteTour,
            $this->getOmsQueryContainer(),
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->getProductQueryContainer(),
            $this->getFacade()->getEdifactReferenceGenerator(),
            $this->getBillingFacade(),
            $this->getEdifactFacade()
        );
    }

    /**
     * @param int $idConcreteTour
     * @return ReadStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDepositExportReadStream(int $idConcreteTour): ReadStreamInterface
    {
        return new DepositExportInputStream(
            $idConcreteTour,
            $this
                ->getFacade()
                ->getEdiDepositExportUtil($idConcreteTour, false),
            $this->getEdifactFacade()
        );
    }

    /**
     * @param string $path
     * @return WriteStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTourExportWriteStream(string $path): WriteStreamInterface
    {
        return new TourExportOutputStream(
            $path,
            $this->getFacade()->getTourExportParser(),
            $this->getEdifactFacade(),
            $this->getConfig()
        );
    }

    /**
     * @param string $path
     * @return WriteStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDepositExportWriteStream(string $path): WriteStreamInterface
    {
        return new DepositExportOutputStream(
            $path,
            $this->getFacade()->getTourExportParser(),
            $this->getEdifactFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return EdifactFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getEdifactFacade(): EdifactFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_EDIFACT);
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return AuthFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getAuthFacade(): AuthFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_AUTH);
    }

    /**
     * @return TourToStateMachineBridgeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getStateMachineFacade(): TourToStateMachineBridgeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_STATE_MACHINE);
    }

    /**
     * @return TourToMailBridgeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMailFacade(): TourToMailBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                TourDependencyProvider::FACADE_MAIL
            );
    }

    /**
     * @return GraphhopperFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphhopperFacade() : GraphhopperFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                TourDependencyProvider::FACADE_GRAPHHOPPER
            );
    }

    /**
     * @return SalesFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getSalesFacade() : SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                TourDependencyProvider::FACADE_SALES
            );
    }

    /**
     * @return BillingQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getBillingQueryContainer() : BillingQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_BILLING);
    }

    /**
     * @return BillingFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getBillingFacade() : BillingFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_BILLING);
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_GRAPHMASTERS);
    }

    /**
     * @return array
     */
    public function getGraphMastersTourProcesses(): array
    {
        return [
            new GraphMastersTourExportConfigurationPlugin()
        ];
    }

    /**
     * @return array
     */
    public function getGraphMastersDepositProcesses(): array
    {
        return [
            new GraphMastersDepositExportConfigurationPlugin()
        ];
    }

    /**
     * @return InputStreamPluginInterface
     */
    public function getGraphMastersTourExportInputStreamPlugin(): InputStreamPluginInterface
    {
        return new GraphMastersTourExportInputStreamPlugin();
    }

    /**
     * @return InputStreamPluginInterface
     */
    public function getGraphMastersDepositExportInputStreamPlugin(): InputStreamPluginInterface
    {
        return new GraphMastersDepositExportInputStreamPlugin();
    }

    /**
     * @param int $idGraphmastersTour
     * @return ReadStreamInterface
     * @throws Exception
     */
    public function createGraphMastersTourExportReadStream(int $idGraphmastersTour): ReadStreamInterface
    {
        return new GraphMastersTourExportInputStream(
            $idGraphmastersTour,
            $this->getOmsQueryContainer(),
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->getProductQueryContainer(),
            $this->getFacade()->getEdifactReferenceGenerator(),
            $this->getBillingFacade(),
            $this->getGraphMastersQueryContainer(),
            $this->getEdifactFacade()
        );
    }

    /**
     * @param int $idGraphmastersTour
     * @return ReadStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createGraphMastersDepositExportReadStream(int $idGraphmastersTour): ReadStreamInterface
    {
        return new GraphMastersDepositExportInputStream(
            $idGraphmastersTour,
            $this->getFacade()->getEdiDepositExportUtil($idGraphmastersTour, true),
            $this->getEdifactFacade()
        );
    }

    /**
     * @return GraphMastersQueryContainer
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersQueryContainer(): GraphMastersQueryContainer
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_GRAPHMASTERS);
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getGraphMastersTourExportOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new GraphMastersTourExportOutputStreamPlugin();
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getGraphMastersDepositExportOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new GraphMastersDepositExportOutputStreamPlugin();
    }

    /**
     * @param string $path
     * @return WriteStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createGraphMastersTourExportWriteStream(string $path): WriteStreamInterface
    {
        return new GraphMastersTourExportOutputStream(
            $path,
            $this->getFacade()->getTourExportParser(),
            $this->getEdifactFacade(),
            $this->getConfig()
        );
    }

    /**
     * @param string $path
     * @return WriteStreamInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createGraphMastersDepositExportWriteStream(string $path): WriteStreamInterface
    {
        return new GraphMastersDepositExportOutputStream(
            $path,
            $this->getFacade()->getTourExportParser(),
            $this->getEdifactFacade(),
            $this->getConfig()
        );
    }
}
