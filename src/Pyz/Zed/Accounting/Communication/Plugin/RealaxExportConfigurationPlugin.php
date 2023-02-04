<?php
/**
 * Durst - project - RealaxExportConfigurationPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.20
 * Time: 15:33
 */

namespace Pyz\Zed\Accounting\Communication\Plugin;


use Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Iterator\ProcessIteratorPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Log\MiddlewareLoggerConfigPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class RealaxExportConfigurationPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin
 * @method AccountingCommunicationFactory getFactory()
 */
class RealaxExportConfigurationPlugin extends AbstractPlugin implements ProcessConfigurationPluginInterface
{
    public const PROCESS_NAME = 'REALAX_EXPORT_PROCESS';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getProcessName(): string
    {
        return static::PROCESS_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface
     */
    public function getInputStreamPlugin(): InputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getRealaxExportInputStreamPlugin();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface
     */
    public function getOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getRealaxExportOutputStreamPlugin();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Iterator\ProcessIteratorPluginInterface
     */
    public function getIteratorPlugin(): ProcessIteratorPluginInterface
    {
        return $this
            ->getFactory()
            ->getRealaxExportIteratorPlugin();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface[]
     */
    public function getStagePlugins(): array
    {
        return $this
            ->getFactory()
            ->getRealaxExportStagePluginsStack();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Log\MiddlewareLoggerConfigPluginInterface
     */
    public function getLoggerPlugin(): MiddlewareLoggerConfigPluginInterface
    {
        return $this
            ->getFactory()
            ->getRealaxLoggerConfigPlugin();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface[]
     */
    public function getPreProcessorHookPlugins(): array
    {
        return $this
            ->getFactory()
            ->getRealaxExportPreProcessorPluginsStack();
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PostProcessorHookPluginInterface[]
     */
    public function getPostProcessorHookPlugins(): array
    {
        return $this
            ->getFactory()
            ->getRealaxExportPostProcessorPluginsStack();
    }
}
