<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 15:13
 */

namespace Pyz\Zed\Tour\Communication\Plugin\TourExporter;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;

/**
 * Class TourExportInputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\TourExporter
 * @method TourCommunicationFactory getFactory()
 */
class TourExportInputStreamPlugin extends AbstractPlugin implements InputStreamPluginInterface
{

    protected const PLUGIN_NAME = 'TourExportInputStreamPlugin';

    /**
     * @param string $path
     *
     * @return \SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface
     * @throws \Exception
     */
    public function getInputStream(string $path): ReadStreamInterface
    {
        $idConcreteTour = (int)$path;

        return $this
            ->getFactory()
            ->createTourExportReadStream($idConcreteTour);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}