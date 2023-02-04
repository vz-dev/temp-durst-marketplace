<?php
/**
 * Durst - project - Release.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-06
 * Time: 10:10
 */

namespace Pyz\Zed\DriverApp\Business\Model;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Orm\Zed\DriverApp\Persistence\DstDriverAppRelease;
use Pyz\Zed\DriverApp\Business\Exception\EntityNotFoundException;
use Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface;
use Pyz\Zed\DriverApp\DriverAppConfig;
use Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface;

class Release implements ReleaseInterface
{
    /**
     * @var \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\DriverApp\DriverAppConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface
     */
    protected $apkFileManager;

    /**
     * Release constructor.
     *
     * @param \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\DriverApp\DriverAppConfig $config
     * @param \Pyz\Zed\DriverApp\Business\Model\File\ApkFileManagerInterface $apkFileManager
     */
    public function __construct(
        DriverAppQueryContainerInterface $queryContainer,
        DriverAppConfig $config,
        ApkFileManagerInterface $apkFileManager
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->apkFileManager = $apkFileManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $releaseTransfer
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function addRelease(DriverAppReleaseTransfer $releaseTransfer): DriverAppReleaseTransfer
    {
        $entity = $this
            ->transferToEntity($releaseTransfer);

        $entity->save();

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriverAppRelease
     */
    public function deleteRelease(int $idDriverAppRelease): void
    {
        $entity = $this
            ->getEntityById($idDriverAppRelease);

        $filePath = sprintf(
            '%s/%s',
            $this->config->getUploadPath(),
            $entity->getApkFilePath()
        );

        $this
            ->apkFileManager
            ->deleteFile($filePath);

        $entity->delete();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDriverAppRelease
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getReleaseById(int $idDriverAppRelease): DriverAppReleaseTransfer
    {
        $entity = $this
            ->getEntityById($idDriverAppRelease);

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    public function getLatestRelease(): DriverAppReleaseTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryLatestRelease()
            ->findOne();

        if ($entity == null) {
            return new DriverAppReleaseTransfer();
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param String $currentVersion
     *
     * @return bool
     */
    public function isUpdatable(String $currentVersion): bool
    {
        $latestRelease = $this
            ->getLatestRelease();

        return (trim($latestRelease->getVersion()) !== trim($currentVersion));
    }

    /**
     * @param int $idDriverAppRelease
     *
     * @throws \Pyz\Zed\DriverApp\Business\Exception\EntityNotFoundException
     *
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppRelease
     */
    protected function getEntityById(int $idDriverAppRelease): DstDriverAppRelease
    {
        $entity = $this
            ->queryContainer
            ->queryDriverAppRelease()
            ->findOneByIdDriverAppRelease($idDriverAppRelease);

        if ($entity == null) {
            throw new EntityNotFoundException($idDriverAppRelease);
        }

        return $entity;
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $transfer
     *
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppRelease
     */
    protected function transferToEntity(DriverAppReleaseTransfer $transfer): DstDriverAppRelease
    {
        $entity = new DstDriverAppRelease();
        $entity->fromArray($transfer->toArray());

        return $entity;
    }

    /**
     * @param \Orm\Zed\DriverApp\Persistence\DstDriverAppRelease $entity
     *
     * @return \Generated\Shared\Transfer\DriverAppReleaseTransfer
     */
    protected function entityToTransfer(DstDriverAppRelease $entity): DriverAppReleaseTransfer
    {
        return (new DriverAppReleaseTransfer())
            ->fromArray($entity->toArray(), true);
    }
}
