<?php

namespace Pyz\Zed\DriverApp\Business;

use Pyz\Zed\DriverApp\Business\Model\File\ApkFileManager;
use Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface;
use Pyz\Zed\DriverApp\Business\Model\Release;
use Pyz\Zed\DriverApp\Business\Model\ReleaseInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method \Pyz\Zed\DriverApp\DriverAppConfig getConfig()
 * @method \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainer getQueryContainer()
 */
class DriverAppBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\DriverApp\Business\Model\ReleaseInterface
     */
    public function createReleaseModel(): ReleaseInterface
    {
        return new Release(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->createApkFileManager()
        );
    }

    /**
     * @return \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface
     */
    protected function createApkFileManager(): ApkFileManagerInterface
    {
        return new ApkFileManager(
            $this->createFileSystem()
        );
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFileSystem(): Filesystem
    {
        return new Filesystem();
    }
}
