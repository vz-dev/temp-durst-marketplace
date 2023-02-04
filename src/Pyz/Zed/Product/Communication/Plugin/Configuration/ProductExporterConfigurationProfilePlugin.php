<?php
/**
 * Durst - project - ProductExporterConfigurationProfilePluginin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.09.18
 * Time: 17:46
 */

namespace Pyz\Zed\Product\Communication\Plugin\Configuration;


use Pyz\Zed\Product\Communication\ProductCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ConfigurationProfilePluginInterface;

/**
 * Class ProductExporterConfigurationProfilePlugin
 * @package Pyz\Zed\Product\Communication\Plugin\Configuration
 * @method ProductCommunicationFactory getFactory()
 */
class ProductExporterConfigurationProfilePlugin extends AbstractPlugin implements ConfigurationProfilePluginInterface
{
    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface[]
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this->getFactory()->getProductExporterProcesses();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\TranslatorFunction\TranslatorFunctionPluginInterface[]
     */
    public function getTranslatorFunctionPlugins(): array
    {
        return [];
    }

    /**
     * @api
     *
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Validator\ValidatorPluginInterface[]
     */
    public function getValidatorPlugins(): array
    {
        return [];
    }
}