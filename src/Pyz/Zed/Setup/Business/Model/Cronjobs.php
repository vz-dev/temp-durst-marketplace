<?php
/**
 * Durst - project - Cronjobs.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.12.18
 * Time: 09:13
 */

namespace Pyz\Zed\Setup\Business\Model;

use Pyz\Shared\Config\Environment;
use Spryker\Zed\Setup\Business\Model\Cronjobs as SprykerCronjobs;

class Cronjobs extends SprykerCronjobs
{
    public const ENV_VAR_ENVIRONMENT = 'APPLICATION_ENV';
    public const ENV_VAR_STORE = 'APPLICATION_STORE';

    /**
     * @var \Pyz\Zed\Setup\SetupConfig
     */
    protected $config;

    /**
     * @param string $command
     * @param string $store
     *
     * @return string
     */
    protected function getCommand($command, $store): string
    {
        if (Environment::isDocker()) {
            return $this->getDockerCommand($command, $store);
        }

        return $this->getDefaultCommand($command, $store);
    }

    /**
     * @param string $command
     * @param string $store
     *
     * @return string
     */
    protected function getDefaultCommand($command, $store): string
    {
        $environment = Environment::getInstance();
        $environment_name = $environment->getEnvironment();
        if ($environment->isNotDevelopment()) {
            $jenkinsScript = $this->config->getDockerScriptPath();
            $phpBinary = $this->config->getPhpBinaryPath();
            $containerId = gethostname();
            $consoleExecutable = APPLICATION_ROOT_DIR . '/vendor/bin/console';
            $envEnvironment = sprintf('%s=%s', static::ENV_VAR_ENVIRONMENT, $environment_name);
            $envStore = sprintf('%s=%s', static::ENV_VAR_STORE, $store);

            return sprintf(
                '<command>%s %s %s %s %s %s %s</command>',
                $jenkinsScript,
                $containerId,
                $phpBinary,
                $consoleExecutable,
                $command,
                $envEnvironment,
                $envStore
            );
        }

        $command = sprintf(
            '$PHP_BIN vendor/bin/console %s',
            $command
        );

        return "<command>
export APPLICATION_ENV=$environment_name
export APPLICATION_STORE=$store
cd /data/shop/development/current
. ./config/Zed/cronjobs/cron.conf
$command</command>";
    }

    /**
     * @param $command
     * @param $store
     *
     * @return string
     */
    protected function getDockerCommand($command, $store): string
    {
        $commandTemplate = "<command>
export APPLICATION_ENV=%s
export APPLICATION_STORE=%s
export COMMAND='%s'
%s
</command>";

        $customBashCommand = 'bash /usr/bin/spryker.sh';

        $command = 'vendor/bin/console ' . $command;

        return sprintf(
            $commandTemplate,
            APPLICATION_ENV,
            $store,
            $command,
            $customBashCommand
        );
    }
}
