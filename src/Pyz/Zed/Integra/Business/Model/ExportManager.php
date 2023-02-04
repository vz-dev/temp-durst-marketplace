<?php
/**
 * Durst - project - OpenOrdersExportManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 13:23
 */

namespace Pyz\Zed\Integra\Business\Model;

use Pyz\Zed\Integra\Business\Model\Connection\FtpManager;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManagerInterface;
use Pyz\Zed\Integra\Business\Model\Export\ExportInterface;
use Pyz\Zed\Integra\IntegraConfig;
use Symfony\Component\Filesystem\Filesystem;

class ExportManager implements ExportManagerInterface
{
    protected const DELIMITER = '|';

    /**
     * @var int
     */
    protected $iterations = 0;

    /**
     * @var IntegraConfig
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var IntegraCredentialsInterface
     */
    protected $credentials;

    /**
     * @var FtpManagerInterface
     */
    protected $ftpManager;

    /**
     * @var ExportInterface
     */
    protected $export;

    /**
     * @var DeliveryNoteManagerInterface
     */
    protected $deliveryNoteManager;

    /**
     * ExportManager constructor.
     *
     * @param IntegraConfig $config
     * @param Filesystem $filesystem
     * @param IntegraCredentialsInterface $credentials
     * @param FtpManagerInterface $ftpManager
     * @param ExportInterface $export
     * @param DeliveryNoteManagerInterface $deliveryNoteManager
     */
    public function __construct(
        IntegraConfig $config,
        Filesystem $filesystem,
        IntegraCredentialsInterface $credentials,
        FtpManagerInterface $ftpManager,
        ExportInterface $export,
        DeliveryNoteManagerInterface $deliveryNoteManager
    ) {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->credentials = $credentials;
        $this->ftpManager = $ftpManager;
        $this->export = $export;
        $this->deliveryNoteManager = $deliveryNoteManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportOrders(int $idBranch): void
    {
        $this->iterations = 0;

        $mappedData = $this->export->getMappedData($idBranch);

        if(count($mappedData) < 2){
            return;
        }

        $absoluteFilename = $this->writeCsvFile($mappedData);
        $this
            ->ftpManager
            ->sendFile(
                $this
                    ->credentials
                    ->getCredentialsByIdBranch($idBranch),
                $absoluteFilename,
                $this->export->getExportType()
            );

        if($this->export->getExportType() === FtpManager::TRANSFER_TYPE_CLOSED)
        {
            $deliveryNoteOrders = $this->export->getExternalIdOrdersByBranchId($idBranch);
            $deliveryNotePdfTransfers = $this->deliveryNoteManager->createDeliveryNotePdfs($deliveryNoteOrders, $idBranch);

            foreach ($deliveryNotePdfTransfers as $deliveryNotePdfTransfer)
            {
                $this
                    ->ftpManager
                    ->sendFile(
                        $this
                            ->credentials
                            ->getCredentialsByIdBranch($idBranch),
                        $deliveryNotePdfTransfer->getFileName(),
                        $this->export->getExportType()
                    );
            }
        }

        $this->export->updateOrders();
        $this->deleteTmpFile($absoluteFilename);
    }

    /**
     * @param string $filename
     *
     * @return void
     */
    protected function deleteTmpFile(string $filename): void
    {
        $this
            ->filesystem
            ->remove($this->getFilenameWithPath($filename));
    }

    /**
     * @param iterable $mappedData
     *
     * @return string
     */
    protected function writeCsvFile(iterable $mappedData): string
    {
        $absoluteFilename = $this->getFilenameWithPath($this->createUniqueFilename());
        $handle = fopen($absoluteFilename, 'a');
        try {
            foreach ($mappedData as $row) {
                fputcsv(
                    $handle,
                    $row,
                    static::DELIMITER
                );
            }
        } finally {
            fclose($handle);
        }

        return $absoluteFilename;
    }

    /**
     * @return string
     */
    protected function createUniqueFilename(): string
    {
        $filename = sprintf(
            '%s.csv',
            uniqid()
        );
        if ($this->filesystem->exists($this->getFilenameWithPath($filename)) === true && $this->iterations < 10) {
            $this->iterations++;
            return $this->createUniqueFilename();
        }

        return $filename;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getFilenameWithPath(string $filename): string
    {
        return sprintf(
            '%s/%s',
            $this->getFilepath(),
            $filename
        );
    }

    /**
     * @return string
     */
    protected function getFilepath(): string
    {
        if ($this->filesystem->exists($this->config->getCsvFileTmpPath()) !== true) {
            $this->filesystem->mkdir($this->config->getCsvFileTmpPath());
        }

        return $this->config->getCsvFileTmpPath();
    }
}
