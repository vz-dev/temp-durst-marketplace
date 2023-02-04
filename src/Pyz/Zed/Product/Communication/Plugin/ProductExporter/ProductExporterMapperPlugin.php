<?php
/**
 * Durst - project - ProductExporterMapperPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 20:07
 */

namespace Pyz\Zed\Product\Communication\Plugin\ProductExporter;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\StagePluginInterface;

/**
 * Class ProductExporterMapperPlugin
 * @package Pyz\Zed\Product\Communication\Plugin\ProductExporter
 * @method \Pyz\Zed\Product\Business\ProductFacadeInterface getFacade()
 * @method \Pyz\Zed\Product\Communication\ProductCommunicationFactory getFactory()
 */
class ProductExporterMapperPlugin extends AbstractPlugin implements StagePluginInterface
{
    protected const PLUGIN_NAME = 'ProductExporterMapperStagePlugin';

    /**
     * @param mixed $payload
     * @param \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface $outStream
     * @param mixed $originalPayload
     *
     * @return array|mixed
     */
    public function process($payload, WriteStreamInterface $outStream, $originalPayload)
    {
        $mapper = $this->getFacade()
                    ->getProductExporterMapper();

        return $mapper->map($payload);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::PLUGIN_NAME;
    }
}
