<?php
/**
 * Durst - project - ProductExporterWriteStreamPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 15:42
 */

namespace Pyz\Zed\Product\Communication\Plugin\ProductExporter;


use Pyz\Zed\Product\Business\Stream\ProductExporterWriteStream;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\Stream\Json\JsonObjectWriteStream;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

class ProductExporterWriteStreamPlugin extends AbstractPlugin implements OutputStreamPluginInterface
{
    public const PLUGIN_NAME = 'ProductExporterWriteStreamPlugin';

    /**
     * @param string $path
     *
     * @return \SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface
     */
    public function getOutputStream(string $path): WriteStreamInterface
    {
        return new ProductExporterWriteStream($path);
    }

    public function getName() : string
    {
        return static::PLUGIN_NAME;
    }

}