<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Setup;

use Pyz\Shared\Setup\SetupConstants;
use Spryker\Zed\Cache\Communication\Console\EmptyAllCachesConsole;
use Spryker\Zed\Installer\Communication\Console\InitializeDatabaseConsole;
use Spryker\Zed\Propel\Communication\Console\PropelInstallConsole;
use Spryker\Zed\Search\Communication\Console\SearchConsole;
use Spryker\Zed\Setup\Communication\Console\EmptyGeneratedDirectoryConsole;
use Spryker\Zed\Setup\SetupConfig as SprykerSetupConfig;
use Spryker\Zed\Transfer\Communication\Console\GeneratorConsole;
use Spryker\Zed\ZedNavigation\Communication\Console\BuildNavigationConsole;

class SetupConfig extends SprykerSetupConfig
{
    /**
     * The following commands are a boilerplate stack. Please customize for your project.
     *
     * For a first initial migration you must use PropelInstallConsole with OPTION_NO_DIFF set to false.
     *
     * @return array
     */
    public function getSetupInstallCommandNames()
    {
        return [
            EmptyAllCachesConsole::COMMAND_NAME,
            EmptyGeneratedDirectoryConsole::COMMAND_NAME,
            // Important note: After first initial migration you must use
            // PropelInstallConsole::COMMAND_NAME => ['--' . PropelInstallConsole::OPTION_NO_DIFF => true]
            // from there on to persist migration files.
            PropelInstallConsole::COMMAND_NAME,
            GeneratorConsole::COMMAND_NAME,
            InitializeDatabaseConsole::COMMAND_NAME,
            BuildNavigationConsole::COMMAND_NAME,
            SearchConsole::COMMAND_NAME,
        ];
    }

    /**
     * @return string
     */
    public function getDockerScriptPath(): string
    {
        return $this
            ->get(SetupConstants::DOCKER_SCRIPT_PATH);
    }

    /**
     * @return string
     */
    public function getPhpBinaryPath(): string
    {
        return $this
            ->get(SetupConstants::PHP_BINARY_PATH);
    }
}
