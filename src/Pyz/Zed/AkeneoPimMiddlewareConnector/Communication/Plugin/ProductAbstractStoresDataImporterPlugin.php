<?php
/**
 * Durst - project - ProductAbstractStoresDataImporterPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.19
 * Time: 16:08
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Dependency\Plugin\DataImporterPluginInterface;

class ProductAbstractStoresDataImporterPlugin extends AbstractPlugin implements DataImporterPluginInterface
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
        // implementation intentionally left blank as we only have one store
        return;
    }
}