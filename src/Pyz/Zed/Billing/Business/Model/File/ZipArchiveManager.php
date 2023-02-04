<?php
/**
 * Durst - project - ZipArchiveManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.03.20
 * Time: 08:30
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use Generated\Shared\Transfer\BillingPeriodTransfer;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Exception\CouldNotCreateZipArchiveException;
use ZipArchive;

class ZipArchiveManager implements ZipArchiveManagerInterface
{
    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\File\PathManagerInterface
     */
    protected $pathManager;

    /**
     * ZipArchiveManager constructor.
     *
     * @param \Pyz\Zed\Billing\BillingConfig $config
     * @param \Pyz\Zed\Billing\Business\Model\File\PathManagerInterface $pathManager
     */
    public function __construct(
        BillingConfig $config,
        PathManagerInterface $pathManager
    ) {
        $this->config = $config;
        $this->pathManager = $pathManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $fileNames
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    public function zipFilesAndGetPath(array $fileNames, BillingPeriodTransfer $billingPeriodTransfer): string
    {
        $this->pathManager->checkZipFilePath();
        $zipArchive = $this->createZipArchive();
        $filePath = $this->getFileName($billingPeriodTransfer);
        if ($zipArchive->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw CouldNotCreateZipArchiveException::build(
                $filePath,
                $this->config->getBillingPeriodZipArchiveTempPath()
            );
        }
        try {
            foreach ($fileNames as $fileName) {
                $zipArchive->addFile($fileName, $this->cutFileName($fileName));
            }
        } finally {
            $zipArchive->close();
        }

        return $filePath;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function cutFileName(string $fileName): string
    {
        $pathElements = explode('/', $fileName);
        return array_pop($pathElements);
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return string
     */
    protected function getFileName(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        return sprintf(
            '%s/%s',
            $this->config->getBillingPeriodZipArchiveTempPath(),
            $this->config->getZipArchiveFileName(
                $billingPeriodTransfer->getBranch()->getIdBranch(),
                $billingPeriodTransfer->getBillingReference()
            )
        );
    }

    /**
     * @return \ZipArchive
     */
    protected function createZipArchive(): ZipArchive
    {
        return new ZipArchive();
    }
}
