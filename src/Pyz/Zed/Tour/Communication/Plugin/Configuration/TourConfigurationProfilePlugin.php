<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 14:09
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Configuration;


use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ConfigurationProfilePluginInterface;

/**
 * Class TourConfigurationProfilePlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\Configuration
 * @method TourCommunicationFactory getFactory()
 */
class TourConfigurationProfilePlugin extends AbstractPlugin implements ConfigurationProfilePluginInterface
{

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface[]
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this
            ->getFactory()
            ->getTourProcesses();
    }

    /**
     * @return \SprykerMiddleware\Zed\Process\Dependency\Plugin\TranslatorFunction\TranslatorFunctionPluginInterface[]
     */
    public function getTranslatorFunctionPlugins(): array
    {
        return $this
            ->getFactory()
            ->getTourTranslatorFunctions();
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