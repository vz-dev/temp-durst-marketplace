<?php
/**
 * Durst - project - RealaxExportFixedOutputStreamPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.09.20
 * Time: 10:33
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class RealaxExportFixedOutputStreamPlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\RealaxExporter
 * @method \Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory getFactory()
 */
class RealaxExportFixedOutputStreamPlugin extends AbstractPlugin implements OutputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'RealaxExportFixedOutputStreamPlugin';

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
            ->createRealaxExportFixedWriteStream(
                $path
            );
    }
}
