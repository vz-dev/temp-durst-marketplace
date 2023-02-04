<?php
/**
 * Durst - project - AccountingCommunicationFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.20
 * Time: 15:29
 */

namespace Pyz\Zed\Accounting\Communication;


use Pyz\Zed\Accounting\AccountingConfig;
use Pyz\Zed\Accounting\AccountingDependencyProvider;
use Pyz\Zed\Accounting\Business\AccountingFacadeInterface;
use Pyz\Zed\Accounting\Business\Stream\RealaxExportFixedInputStream;
use Pyz\Zed\Accounting\Business\Stream\RealaxExportFixedOutputStream;
use Pyz\Zed\Accounting\Business\Stream\RealaxExportInputStream;
use Pyz\Zed\Accounting\Business\Stream\RealaxExportOutputStream;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExportConfigurationPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter\RealaxExportFixedInputStreamPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter\RealaxExportFixedOutputStreamPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter\RealaxExportInputStreamPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter\RealaxExportMapperPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter\RealaxExportOutputStreamPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExportFixedConfigurationPlugin;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridgeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Communication\Plugin\Iterator\NullIteratorPlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\Log\MiddlewareLoggerConfigPlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\StreamReaderStagePlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\StreamWriterStagePlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Iterator\ProcessIteratorPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class AccountingCommunicationFactory
 * @package Pyz\Zed\Accounting\Communication
 * @method AccountingFacadeInterface getFacade()
 * @method AccountingConfig getConfig()
 */
class AccountingCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return array
     */
    public function getRealaxProcesses(): array
    {
        return [
            new RealaxExportConfigurationPlugin()
        ];
    }

    /**
     * @return array
     */
    public function getRealaxFixedProcesses(): array
    {
        return [
            new RealaxExportFixedConfigurationPlugin()
        ];
    }

    /**
     * @return array
     */
    public function getRealaxTranslatorFunctions(): array
    {
        return [];
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface
     */
    public function getRealaxExportInputStreamPlugin(): InputStreamPluginInterface
    {
        return new RealaxExportInputStreamPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface
     */
    public function getRealaxExportFixedInputStreamPlugin(): InputStreamPluginInterface
    {
        return new RealaxExportFixedInputStreamPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface
     */
    public function getRealaxExportOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new RealaxExportOutputStreamPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface
     */
    public function getRealaxExportFixedOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return new RealaxExportFixedOutputStreamPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Iterator\ProcessIteratorPluginInterface
     */
    public function getRealaxExportIteratorPlugin(): ProcessIteratorPluginInterface
    {
        return new NullIteratorPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface[]
     */
    public function getRealaxExportStagePluginsStack(): array
    {
        return [
            new StreamReaderStagePlugin(),
            new RealaxExportMapperPlugin(),
            new StreamWriterStagePlugin()
        ];
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Communication\Plugin\Log\MiddlewareLoggerConfigPlugin
     */
    public function getRealaxLoggerConfigPlugin(): MiddlewareLoggerConfigPlugin
    {
        return new MiddlewareLoggerConfigPlugin();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface[]
     */
    public function getRealaxExportPreProcessorPluginsStack(): array
    {
        return [];
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PostProcessorHookPluginInterface[]
     */
    public function getRealaxExportPostProcessorPluginsStack(): array
    {
        return [];
    }

    /**
     * @param int $idMerchant
     * @return \SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface
     * @throws \Exception
     */
    public function createRealaxExportReadStream(int $idMerchant): ReadStreamInterface
    {
        return new RealaxExportInputStream(
            $idMerchant,
            $this->getFacade(),
            $this->getConfig()
        );
    }

    /**
     * @param int $idMerchant
     * @return \SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface
     * @throws \Exception
     */
    public function createRealaxExportFixedReadStream(int $idMerchant): ReadStreamInterface
    {
        return new RealaxExportFixedInputStream(
            $idMerchant,
            $this->getFacade(),
            $this->getConfig()
        );
    }

    /**
     * @param string $path
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRealaxExportWriteStream(string $path): WriteStreamInterface
    {
        return new RealaxExportOutputStream(
            $path,
            $this->getMailFacade(),
            $this->getConfig()
        );
    }

    /**
     * @param string $path
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRealaxExportFixedWriteStream(string $path): WriteStreamInterface
    {
        return new RealaxExportFixedOutputStream(
            $path,
            $this->getMailFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailFacade(): AccountingToMailBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::FACADE_MAIL
            );
    }
}
