<?php

namespace Pyz\Zed\Tour\Communication\Plugin\TourExporter;

use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;

/**
 * Class GraphMastersTourExportInputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\TourExporter
 */
class GraphMastersTourExportInputStreamPlugin extends TourExportInputStreamPlugin
{
    protected const PLUGIN_NAME = 'GraphMastersTourExportInputStreamPlugin';

    /**
     * @param string $path
     *
     * @return ReadStreamInterface
     *
     * @throws ContainerKeyNotFoundException
     */
    public function getInputStream(string $path): ReadStreamInterface
    {
        $idGraphmastersTour = (int) $path;

        return $this
            ->getFactory()
            ->createGraphMastersTourExportReadStream($idGraphmastersTour);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}
