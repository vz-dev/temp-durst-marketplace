<?php

namespace Pyz\Zed\SetupFrontend\Business;

use Pyz\Zed\SetupFrontend\Business\Model\Installer\PathFinder\InstallPathFinder;
use Pyz\Zed\SetupFrontend\SetupFrontendConfig;
use Pyz\Zed\SetupFrontend\Business\Model\Installer\DependencyInstaller;
use Spryker\Zed\SetupFrontend\Business\Model\Installer\DependencyInstallerInterface;
use Spryker\Zed\SetupFrontend\Business\Model\Installer\PathFinder\PathFinderInterface;
use Spryker\Zed\SetupFrontend\Business\SetupFrontendBusinessFactory as SprykerSetupFrontendBusinessFactory;

/**
 * @method SetupFrontendConfig getConfig()
 */
class SetupFrontendBusinessFactory extends SprykerSetupFrontendBusinessFactory
{
    /**
     * @return DependencyInstallerInterface
     */
    public function createZedDependencyInstaller()
    {
        return new DependencyInstaller(
            $this->createZedInstallerPathFinder(),
            $this->getConfig()->getZedInstallCommand(),
            $this->getConfig()->getZedCiInstallCommand()
        );
    }

    /**
     * @return PathFinderInterface
     */
    protected function createZedInstallerPathFinder()
    {
        return new InstallPathFinder($this->getConfig()->getZedInstallerDirectoryPatterns());
    }
}
