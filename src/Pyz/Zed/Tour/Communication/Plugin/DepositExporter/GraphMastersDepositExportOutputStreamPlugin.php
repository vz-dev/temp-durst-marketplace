<?php

namespace Pyz\Zed\Tour\Communication\Plugin\DepositExporter;

use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;

/**
 * @method TourCommunicationFactory getFactory()
 */
class GraphMastersDepositExportOutputStreamPlugin extends DepositExportOutputStreamPlugin
{
    protected const PLUGIN_NAME = 'GraphMastersDepositExportOutputStreamPlugin';

    /**
     * @param string $path
     *
     * @return WriteStreamInterface
     *
     * @throws ContainerKeyNotFoundException
     */
    public function getOutputStream(string $path): WriteStreamInterface
    {
        return $this
            ->getFactory()
            ->createGraphMastersDepositExportWriteStream($path);
    }
}
