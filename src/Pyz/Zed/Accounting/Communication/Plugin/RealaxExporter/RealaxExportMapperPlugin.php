<?php
/**
 * Durst - project - RealaxExportMapperPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.03.20
 * Time: 11:32
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter;


use Pyz\Zed\Accounting\Business\AccountingFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface;

/**
 * Class RealaxExportMapperPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter
 * @method AccountingFacadeInterface getFacade()
 */
class RealaxExportMapperPlugin extends AbstractPlugin implements StagePluginInterface
{
    protected const PLUGIN_NAME = 'RealaxExporterMapperStagePlugin';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::PLUGIN_NAME;
    }

    /**
     * @param mixed $payload
     * @param \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface $outStream
     * @param mixed $originalPayload
     * @return array
     */
    public function process($payload, WriteStreamInterface $outStream, $originalPayload): array
    {
        return $this
            ->getFacade()
            ->mapRealaxExport(
                $payload
            );
    }
}
