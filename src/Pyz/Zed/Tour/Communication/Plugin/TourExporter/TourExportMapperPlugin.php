<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-04
 * Time: 16:13
 */

namespace Pyz\Zed\Tour\Communication\Plugin\TourExporter;


use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface;

/**
 * Class TourExportMapperPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\TourExporter
 * @method TourFacadeInterface getFacade()
 */
class TourExportMapperPlugin extends AbstractPlugin implements StagePluginInterface
{
    protected const PLUGIN_NAME = 'TourExporterMapperStagePlugin';

    /**
     * @param mixed $payload
     * @param WriteStreamInterface $outStream
     * @param mixed $originalPayload
     *
     * @return mixed
     */
    public function process($payload, WriteStreamInterface $outStream, $originalPayload)
    {
        $mapper = $this
            ->getFacade()
            ->getTourExportMapper();

        return $mapper
            ->map($payload);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::PLUGIN_NAME;
    }
}