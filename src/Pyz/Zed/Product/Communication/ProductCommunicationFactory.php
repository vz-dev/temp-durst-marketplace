<?php
/**
 * Durst - project - ProductCommunicationFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.09.18
 * Time: 18:03
 */

namespace Pyz\Zed\Product\Communication;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Product\Business\Stream\ProductExporterReadStream;
use Pyz\Zed\Product\Communication\Plugin\ProductExporter\ProductExporterPlugin;
use Pyz\Zed\Product\ProductDependencyProvider;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Product\Communication\ProductCommunicationFactory as SprykerProductCommunicationFactory;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;


class ProductCommunicationFactory extends SprykerProductCommunicationFactory
{
    /**
     * @return array
     */
    public function getProductExporterProcesses()
    {
        return [
            new ProductExporterPlugin(),
        ];
    }

    /**
     * @return ReadStreamInterface
     */
    public function createProductExporterReadStream() : ReadStreamInterface
    {
        return new ProductExporterReadStream(
            $this->getQueryContainer()->queryProduct()
        );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(ProductDependencyProvider::FACADE_MERCHANT);
    }
}
