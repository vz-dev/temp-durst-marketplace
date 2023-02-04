<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-17
 * Time: 15:29
 */

namespace Pyz\Zed\Tour\Communication\Plugin\DepositExporter;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;

/**
 * Class DepositExportInputStreamPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\DepositExporter
 * @method TourCommunicationFactory getFactory()
 */
class DepositExportInputStreamPlugin extends AbstractPlugin implements InputStreamPluginInterface
{
    protected const PLUGIN_NAME = 'DepositExportInputStreamPlugin';

    /**
     * @param string $path
     *
     * @return \SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface
     */
    public function getInputStream(string $path): ReadStreamInterface
    {
        $idConcreteTour = (int)$path;

        return $this
            ->getFactory()
            ->createDepositExportReadStream($idConcreteTour);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}