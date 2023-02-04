<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-17
 * Time: 15:32
 */

namespace Pyz\Zed\Tour\Communication\Plugin\DepositExporter;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class DepositExportOutputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\DepositExporter
 * @method TourCommunicationFactory getFactory()
 */
class DepositExportOutputStreamPlugin extends AbstractPlugin implements OutputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'DepositExportOutputStreamPlugin';

    /**
     * @param string $path
     *
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     */
    public function getOutputStream(string $path): WriteStreamInterface
    {
        return $this
            ->getFactory()
            ->createDepositExportWriteStream($path);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}
