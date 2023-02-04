<?php

namespace Pyz\Zed\SetupFrontend\Business\Model\Installer\PathFinder;

use Symfony\Component\Finder\Finder;
use Spryker\Zed\SetupFrontend\Business\Model\Installer\PathFinder\InstallPathFinder as SprykerInstallPathFinder;

class InstallPathFinder extends SprykerInstallPathFinder
{
    /**
     * @var array
     */
    protected $pathPatterns;

    /**
     * @param array $pathPatterns
     */
    public function __construct(array $pathPatterns)
    {
        parent::__construct('');

        $this->pathPatterns = $pathPatterns;
    }

    /**
     * @return Finder
     */
    public function find(): Finder
    {
        $finder = new Finder();

        $finder
            ->files()
            ->in($this->pathPatterns)
            ->name('package.json')
            ->depth('< 2');

        return $finder;
    }
}
