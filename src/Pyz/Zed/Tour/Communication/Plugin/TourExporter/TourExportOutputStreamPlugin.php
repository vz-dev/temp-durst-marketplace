<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 16:01
 */

namespace Pyz\Zed\Tour\Communication\Plugin\TourExporter;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class TourExportOutputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\TourExporter
 * @method TourCommunicationFactory getFactory()
 */
class TourExportOutputStreamPlugin extends AbstractPlugin implements OutputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'TourExportOutputStreamPlugin';

    /**
     * @param string $path
     *
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     */
    public function getOutputStream(string $path): WriteStreamInterface
    {
        return $this
            ->getFactory()
            ->createTourExportWriteStream($path);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}