<?php
/**
 * Durst - project - RealaxConfigurationProfilePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.20
 * Time: 15:27
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\Configuration;


use Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ConfigurationProfilePluginInterface;

/**
 * Class RealaxConfigurationProfilePlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\Configuration
 * @method AccountingCommunicationFactory getFactory()
 */
class RealaxConfigurationProfilePlugin extends AbstractPlugin implements ConfigurationProfilePluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this
            ->getFactory()
            ->getRealaxProcesses();
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getTranslatorFunctionPlugins(): array
    {
        return $this
            ->getFactory()
            ->getRealaxTranslatorFunctions();
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getValidatorPlugins(): array
    {
        return [];
    }
}
