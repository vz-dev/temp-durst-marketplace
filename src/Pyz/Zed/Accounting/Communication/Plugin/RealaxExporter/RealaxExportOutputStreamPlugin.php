<?php
/**
 * Durst - project - RealaxExportOutputStreamPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.03.20
 * Time: 14:42
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter;


use Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class RealaxExportOutputStreamPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter
 * @method AccountingCommunicationFactory getFactory()
 */
class RealaxExportOutputStreamPlugin extends AbstractPlugin implements OutputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'RealaxExportOutputStreamPlugin';

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
     * {@inheritDoc}
     *
     * @param string $path
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getOutputStream(string $path): WriteStreamInterface
    {
        return $this
            ->getFactory()
            ->createRealaxExportWriteStream(
                $path
            );
    }
}
