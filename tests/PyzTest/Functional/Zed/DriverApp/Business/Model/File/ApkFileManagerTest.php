<?php
namespace PyzTest\Functional\Zed\DriverApp\Business\Model\File;

use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\DriverApp\Business\Exception\FileNotFoundException;
use Pyz\Zed\DriverApp\Business\Model\File\ApkFileManager;
use Symfony\Component\Filesystem\Filesystem;

class ApkFileManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \PyzTest\Functional\Zed\DriverApp\DriverAppBusinessTester
     */
    protected $tester;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem|MockObject
     */
    protected $fileSystem;

    /**
     * @var \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface
     */
    protected $apkFileManager;

    protected function _before()
    {
        $this->apkFileManager = $this->createApkFileManager();
    }

    /**
     * @return \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManager
     */
    protected function createApkFileManager()
    {
        return new ApkFileManager(
            $this->createFileSystemMock()
        );
    }

    /**
     * @return MockObject|Filesystem
     */
    protected function createFileSystemMock()
    {
        $this->fileSystem =  $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods([
                'remove',
                'exists',
            ])
            ->getMock();

        return $this->fileSystem;
    }

    public function testDeleteFileChecksFileExistenceAndThrowsException()
    {
        $this
            ->fileSystem
            ->expects($this->once())
            ->method('exists');

        $this
            ->expectException(FileNotFoundException::class);

        $this
            ->apkFileManager
            ->deleteFile('test');
    }

    public function testDeleteFileCallsFileSystemDeleteMethod()
    {
        $this
            ->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this
            ->fileSystem
            ->expects($this->once())
            ->method('remove');

        $this
            ->apkFileManager
            ->deleteFile('test');
    }
}
