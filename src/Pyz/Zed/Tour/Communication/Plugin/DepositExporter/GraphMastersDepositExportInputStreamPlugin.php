<?php

namespace Pyz\Zed\Tour\Communication\Plugin\DepositExporter;

use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;

/**
 * Class GraphMastersDepositExportInputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\DepositExporter
 */
class GraphMastersDepositExportInputStreamPlugin extends DepositExportInputStreamPlugin
{
    protected const PLUGIN_NAME = 'GraphMastersDepositExportInputStreamPlugin';

    /**
     * @param string $path
     *
     * @return ReadStreamInterface
     */
    public function getInputStream(string $path): ReadStreamInterface
    {
        $idGraphmastersTour = (int) $path;

        return $this
            ->getFactory()
            ->createGraphMastersDepositExportReadStream($idGraphmastersTour);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}
