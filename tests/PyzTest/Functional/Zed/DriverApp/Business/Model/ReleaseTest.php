<?php
namespace PyzTest\Functional\Zed\DriverApp\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Orm\Zed\DriverApp\Persistence\DstDriverAppRelease;
use Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery;
use PHPUnit\Framework\MockObject\MockObject;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DriverApp\Business\Model\File\ApkFileManager;
use Pyz\Zed\DriverApp\Business\Model\Release;
use Pyz\Zed\DriverApp\Business\Model\ReleaseInterface;
use Pyz\Zed\DriverApp\DriverAppConfig;
use Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainer;
use Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group DriverApp
 * @group Model
 * @group ReleaseTest
 * Add your own group annotations below this line
 */
class ReleaseTest extends Unit
{
    protected const PATH = 'someFile.apk';
    protected const VERSION = '1.0';
    protected const PATCH_NOTES = 'some patch notes';

    /**
     * @var \PyzTest\Functional\Zed\DriverApp\DriverAppBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface|MockObject
     */
    protected $apkFileManager;

    /**
     * @var \Pyz\Zed\DriverApp\Business\Model\ReleaseInterface
     */
    protected $releaseModel;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->releaseModel = $this->createReleaseModel();
    }

    /**
     * @return \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface
     */
    protected function createQueryContainer(): DriverAppQueryContainerInterface
    {
        return new DriverAppQueryContainer();
    }

    /**
     * @return MockObject|\Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface
     */
    protected function createApkFileManagerMock()
    {
        $this->apkFileManager = $this
            ->getMockBuilder(ApkFileManager::class)
            ->setConstructorArgs([
                $this->createFileSystemMock(),
            ])
            ->setMethods([
                'deleteFile',
            ])
            ->getMock();

        return $this->apkFileManager;
    }

    /**
     * @return MockObject|\Symfony\Component\Filesystem\Filesystem
     */
    protected function createFileSystemMock()
    {
        return $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods([
                'delete',
                'exists',
            ])
            ->getMock();
    }

    /**
     * @return MockObject|\Pyz\Zed\DriverApp\DriverAppConfig
     */
    protected function createConfigMock()
    {
        return $this
            ->getMockBuilder(DriverAppConfig::class)
            ->setMethods([
                'getUploadPath',
            ])
            ->getMock();
    }

    /**
     * @return \Pyz\Zed\DriverApp\Business\Model\ReleaseInterface
     */
    protected function createReleaseModel(): ReleaseInterface
    {
        return new Release(
            $this->createQueryContainer(),
            $this->createConfigMock(),
            $this->createApkFileManagerMock()
        );
    }

    /**
     * @return void
     */
    public function testAddReleasePersistsEntity()
    {
        $transfer = $this->createReleaseTransfer();

        $transfer = $this
            ->releaseModel
            ->addRelease($transfer);

        $this
            ->assertNotNull($transfer->getIdDriverAppRelease());
    }

    /**
     * @return void
     */
    public function testAddReleasePersistsCorrectValues()
    {
        $transfer = $this->createReleaseTransfer();

        $transfer = $this
            ->releaseModel
            ->addRelease($transfer);

        $this->assertSame(self::PATCH_NOTES, $transfer->getPatchNotes());
        $this->assertSame(self::VERSION, $transfer->getVersion());
        $this->assertSame(self::PATH, $transfer->getApkFilePath());
    }

    /**
     * @return void
     */
    public function testAddingReleaseWithExistingVersionThrowsException()
    {
        $this->expectException(PropelException::class);

        $this
            ->releaseModel
            ->addRelease($this->createReleaseTransfer());

        $this
            ->releaseModel
            ->addRelease($this->createReleaseTransfer());
    }

    /**
     * @return void
     */
    public function testDeleteRemovesRelease()
    {
        $entity = new DstDriverAppRelease();
        $entity->setVersion(self::VERSION);
        $entity->setPatchNotes(self::PATCH_NOTES);
        $entity->setApkFilePath(self::PATH);
        $entity->save();

        $this
            ->releaseModel
            ->deleteRelease($entity->getIdDriverAppRelease());

        $this
            ->assertSame(
                0,
                DstDriverAppReleaseQuery::create()
                    ->filterByIdDriverAppRelease($entity->getIdDriverAppRelease())
                    ->count()
            );
    }

    /**
     * @return void
     */
    public function testDeleteCallsDeleteFileFromFileManager()
    {
        $entity = new DstDriverAppRelease();
        $entity->setVersion(self::VERSION);
        $entity->setPatchNotes(self::PATCH_NOTES);
        $entity->setApkFilePath(self::PATH);
        $entity->save();

        $this
            ->apkFileManager
            ->expects(
                $this->once()
            )
            ->method('deleteFile');

        $this
            ->releaseModel
            ->deleteRelease($entity->getIdDriverAppRelease());
    }

    /**
     * @return void
     */
    public function testGetReleaseByIdReturnsCorrectRelease()
    {
        $entity = new DstDriverAppRelease();
        $entity->setVersion(self::VERSION);
        $entity->setPatchNotes(self::PATCH_NOTES);
        $entity->setApkFilePath(self::PATH);
        $entity->save();

        $transfer = $this
            ->releaseModel
            ->getReleaseById($entity->getIdDriverAppRelease());

        $this->assertSame($entity->getVersion(), $transfer->getVersion());
        $this->assertSame($entity->getIdDriverAppRelease(), $transfer->getIdDriverAppRelease());
        $this->assertSame($entity->getApkFilePath(), $transfer->getApkFilePath());
        $this->assertSame($entity->getPatchNotes(), $transfer->getPatchNotes());
    }

    /**
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    protected function createReleaseTransfer(): DriverAppReleaseTransfer
    {
        return (new DriverAppReleaseTransfer())
            ->setApkFilePath(self::PATH)
            ->setPatchNotes(self::PATCH_NOTES)
            ->setVersion(self::VERSION);
    }
}
