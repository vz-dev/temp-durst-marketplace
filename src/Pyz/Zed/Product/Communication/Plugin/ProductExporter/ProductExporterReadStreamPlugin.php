<?php
/**
 * Durst - project - ProductExporterReadStreamPluginphp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 14:16
 */

namespace Pyz\Zed\Product\Communication\Plugin\ProductExporter;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;


/**
 * Class ProductExporterReadStreamPlugin
 * @package Pyz\Zed\Product\Communication\Plugin\Stream
 * @method \Pyz\Zed\Product\Business\ProductFacadeInterface getFacade()
 * @method \Pyz\Zed\Product\Communication\ProductCommunicationFactory getFactory()
 */
class ProductExporterReadStreamPlugin extends AbstractPlugin implements InputStreamPluginInterface
{

    protected const PLUGIN_NAME = 'ProductExporterReadStreamPlugin';

    public function getInputStream(string $path): ReadStreamInterface
    {
        return $this->getFactory()->createProductExporterReadStream();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return static::PLUGIN_NAME;
    }

}