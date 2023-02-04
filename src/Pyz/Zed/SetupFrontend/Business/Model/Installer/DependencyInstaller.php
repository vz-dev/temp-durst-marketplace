<?php

namespace Pyz\Zed\SetupFrontend\Business\Model\Installer;

use Psr\Log\LoggerInterface;
use Spryker\Zed\SetupFrontend\Business\Model\Installer\DependencyInstaller as SprykerDependencyInstaller;
use Spryker\Zed\SetupFrontend\Business\Model\Installer\PathFinder\PathFinderInterface;
use Symfony\Component\Process\Process;

class DependencyInstaller extends SprykerDependencyInstaller
{
    const CI_INSTALL_REGEX = '%vendor/spryker/(discount|product\-relation)%';

    /**
     * @var string
     */
    protected $ciInstallCommand;

    /**
     * @param PathFinderInterface $installPathFinder
     * @param string $installCommand
     * @param string $ciInstallCommand
     */
    public function __construct(
        PathFinderInterface $installPathFinder,
        string $installCommand,
        string $ciInstallCommand
    ) {
        parent::__construct($installPathFinder, $installCommand);

        $this->ciInstallCommand = $ciInstallCommand;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return bool
     */
    public function install(LoggerInterface $logger): bool
    {
        $isSuccess = true;

        foreach ($this->installPathFinder->find() as $file) {
            $path = $file->getPath();

            $logger->info(sprintf('Install dependencies in "%s"', $path));

            $process = new Process(
                preg_match(self::CI_INSTALL_REGEX, $path) !== 1
                    ? $this->installCommand
                    : $this->ciInstallCommand,
                $path
            );

            $process->setTimeout(null);

            $process->run(function ($type, $buffer) use ($logger) {
                $logger->info($buffer);
            });

            if (!$process->isSuccessful()) {
                $isSuccess = false;
            }
        }

        return $isSuccess;
    }
}
