<?php
/**
 * Durst - project - RealaxFixedConfigurationProfilePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.09.20
 * Time: 10:38
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\Configuration;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ConfigurationProfilePluginInterface;

/**
 * Class RealaxFixedConfigurationProfilePlugin
 * @package Pyz\Zed\Accounting\Communication\Plugin\Configuration
 * @method \Pyz\Zed\Accounting\Communication\AccountingCommunicationFactory getFactory()
 */
class RealaxFixedConfigurationProfilePlugin extends AbstractPlugin implements ConfigurationProfilePluginInterface
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
            ->getRealaxFixedProcesses();
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
