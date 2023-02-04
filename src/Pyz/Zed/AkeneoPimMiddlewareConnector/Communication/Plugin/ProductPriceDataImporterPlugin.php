<?php
/**
 * Durst - project - ProductPriceDataImporterPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.19
 * Time: 16:04
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Dependency\Plugin\DataImporterPluginInterface;

class ProductPriceDataImporterPlugin extends AbstractPlugin implements DataImporterPluginInterface
{

    /**
     * @api
     *
     * @param array $data
     *
     * @return void
     */
    public function import(array $data): void
    {
        // implementation intentionally left blank as we don't need to import prices
        return;
    }
}