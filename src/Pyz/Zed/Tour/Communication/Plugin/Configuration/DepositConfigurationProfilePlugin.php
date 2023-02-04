<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-17
 * Time: 16:07
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Configuration;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ConfigurationProfilePluginInterface;

/**
 * Class DepositConfigurationProfilePlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\Configuration
 * @method TourCommunicationFactory getFactory()
 */
class DepositConfigurationProfilePlugin extends AbstractPlugin implements ConfigurationProfilePluginInterface
{

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface[]
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this
            ->getFactory()
            ->getDepositProcesses();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\TranslatorFunction\TranslatorFunctionPluginInterface[]
     */
    public function getTranslatorFunctionPlugins(): array
    {
        return $this
            ->getFactory()
            ->getDepositTranslatorFunctions();
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