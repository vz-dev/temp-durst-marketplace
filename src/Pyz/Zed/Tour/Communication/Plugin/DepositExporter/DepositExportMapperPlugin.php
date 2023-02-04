<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-17
 * Time: 15:26
 */

namespace Pyz\Zed\Tour\Communication\Plugin\DepositExporter;


use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface;

/**
 * Class DepositExportMapperPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\DepositExporter
 * @method TourFacadeInterface getFacade()
 */
class DepositExportMapperPlugin extends AbstractPlugin implements StagePluginInterface
{
    protected const PLUGIN_NAME = 'DepositExporterMapperStagePlugin';

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