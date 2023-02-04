<?php

namespace Pyz\Zed\SetupFrontend;

use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Zed\SetupFrontend\SetupFrontendConfig as SprykerSetupFrontendConfig;

class SetupFrontendConfig extends SprykerSetupFrontendConfig
{
    /**
     * @return array
     */
    public function getZedInstallerDirectoryPatterns(): array
    {
        return [
            $this->get(KernelConstants::SPRYKER_ROOT) . '/*/assets/Zed',
            APPLICATION_ROOT_DIR . '/assets/Zed',
        ];
    }

    /**
     * @return string
     */
    public function getZedCiInstallCommand(): string
    {
        return 'npm ci --prefer-offline';
    }
}
