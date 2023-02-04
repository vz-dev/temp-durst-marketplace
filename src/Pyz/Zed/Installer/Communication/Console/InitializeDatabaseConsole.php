<?php

namespace Pyz\Zed\Installer\Communication\Console;

use Spryker\Zed\Installer\Business\InstallerFacade;
use Spryker\Zed\Installer\Communication\Console\InitializeDatabaseConsole as SprykerInitializeDatabaseConsole;

/**
 * @method InstallerFacade getFacade()
 */
class InitializeDatabaseConsole extends SprykerInitializeDatabaseConsole
{
    /**
     * @param string $className
     *
     * @return mixed
     */
    protected function getPluginNameFromClass($className)
    {
        $pattern = '#^(.+?)\\\(.+?)\\\(.+?)\\\(.+)#i';
        return preg_replace($pattern, '${3}', $className);
    }
}
