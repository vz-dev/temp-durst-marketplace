<?php
/**
 * Durst - project - PriceImportManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:13
 */

namespace Pyz\Zed\PriceImport\Business\Manager;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\PriceImportTransfer;
use Generated\Shared\Transfer\PriceTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Orm\Zed\PriceImport\Persistence\DstPriceImport;
use Orm\Zed\PriceImport\Persistence\Map\DstPriceImportTableMap;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportCouldNotOpenCsvException;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidHeaderException;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidIdException;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidMapperException;
use Pyz\Zed\PriceImport\Communication\Plugin\Mail\BatchPriceImportDeactivatedProductsMailTypePlugin;
use Pyz\Zed\PriceImport\Communication\Plugin\Mail\BatchPriceImportMailTypePlugin;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridgeInterface;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridgeInterface;
use Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridgeInterface;
use Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface;
use Pyz\Zed\PriceImport\PriceImportConfig;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use SplFileObject;

class PriceImportManager implements PriceImportManagerInterface
{
    public const IMPORT_KEY_SKU = 'durst_sku';
    public const IMPORT_KEY_PRODUCT_NAME = 'product_name';
    public const IMPORT_KEY_PRODUCT_UNIT = 'product_unit';
    public const IMPORT_KEY_PRICE_GROSS = 'price_brutto';
    public const IMPORT_KEY_MERCHANT_SKU = 'merchant_sku';
    public const IMPORT_KEY_STATUS = 'product_status';

    public const IMPORT_RETURN_KEY_UPDATED = 'updated';
    public const IMPORT_RETURN_KEY_IGNORED = 'ignored';
    public const IMPORT_RETURN_KEY_DELETED = 'deleted';
    public const IMPORT_RETURN_KEY_CREATED = 'created';

    public const IMPORT_KEY_SKU_POSITION = 0;
    public const IMPORT_KEY_PRODUCT_NAME_POSITION = 1;
    public const IMPORT_KEY_PRODUCT_UNIT_POSITION = 2;
    public const IMPORT_KEY_MERCHANT_SKU_POSITION = 3;
    public const IMPORT_KEY_PRICE_GROSS_POSITION = 4;
    public const IMPORT_KEY_STATUS_POSITION = 5;

    protected const MAIL_SUBJECT = 'Preise wurden importiert';
    protected const MAIL_BODY = 'Die Datei "%s" wurde erfolgreich importiert.';
    protected const MAIL_STATS = 'Aktualisiert: %1$d' . PHP_EOL . 'Angelegt: %4$d' . PHP_EOL . 'Gelöscht: %3$d' . PHP_EOL . 'Übersprungen: %2$d';

    protected const MAIL_SUBJECT_DEACTIVATED_PRODUCT = 'Diese Preise wurden nicht importiert';
    protected const MAIL_BODY_DEACTIVATED_PRODUCT = 'Der csv-import enthält einige deaktivierte produkte, die nicht importiert werden. ';
    /**
     * @var \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridgeInterface
     */
    protected $merchantPriceFacade;

    /**
     * @var \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridgeInterface
     */
    protected $productFacade;

    /**
     * @var \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridgeInterface
     */
    protected $mailFacade;

    /**
     * @var \Pyz\Zed\PriceImport\Business\Mapper\PriceImportMapperInterface[]
     */
    protected $mappers = [];

    /**
     * @var \Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @var \Pyz\Zed\PriceImport\PriceImportConfig
     */
    protected $config;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\PriceImport\Business\Mapper\PriceImportMapperInterface
     */
    protected $assignedMapper;

    /**
     * @var array
     */
    protected $merchantPrices = [];

    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var int
     */
    protected $cntUpdated = 0;

    /**
     * @var int
     */
    protected $cntIgnored = 0;

    /**
     * @var int
     */
    protected $cntDeleted = 0;

    /**
     * @var int
     */
    protected $cntCreated = 0;

    /**
     * @var bool
     */
    protected $isHeaderRow = true;

    /**
     * @var PriceImportTransfer
     */
    protected $priceImportTransfer;

    /**
     * PriceImportManager constructor.
     * @param \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMerchantPriceBridgeInterface $merchantPriceFacade
     * @param \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToProductBridgeInterface $productFacade
     * @param \Pyz\Zed\PriceImport\Dependency\Facade\PriceImportToMailBridgeInterface $mailFacade
     * @param \Pyz\Zed\PriceImport\Business\Mapper\PriceImportMapperInterface[] $mappers
     * @param \Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface $queryContainer
     * @param ProductQueryContainer $productQueryContainer
     * @param \Pyz\Zed\PriceImport\PriceImportConfig $config
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        PriceImportToMerchantPriceBridgeInterface $merchantPriceFacade,
        PriceImportToProductBridgeInterface $productFacade,
        PriceImportToMailBridgeInterface $mailFacade,
        array $mappers,
        PriceImportQueryContainerInterface $queryContainer,
        ProductQueryContainer $productQueryContainer,
        PriceImportConfig $config,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->merchantPriceFacade = $merchantPriceFacade;
        $this->productFacade = $productFacade;
        $this->mailFacade = $mailFacade;
        $this->mappers = $mappers;
        $this->queryContainer = $queryContainer;
        $this->productQueryContainer = $productQueryContainer;
        $this->config = $config;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportCouldNotOpenCsvException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidHeaderException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidIdException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidMapperException
     */
    public function importNext(): array
    {
        $priceImportEntity = $this
            ->queryContainer
            ->queryPriceImportWaiting()
            ->findOne();

        if ($priceImportEntity === null) {
            return [];
        }

        return $this
            ->import(
                $this
                    ->entityToTransfer(
                        $priceImportEntity
                    )
            );
    }

    /**
     * @param \Orm\Zed\PriceImport\Persistence\DstPriceImport $dstPriceImport
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     */
    protected function entityToTransfer(DstPriceImport $dstPriceImport): PriceImportTransfer
    {
        return (new PriceImportTransfer())
            ->fromArray(
                $dstPriceImport
                    ->toArray(),
                true
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportCouldNotOpenCsvException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidHeaderException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidIdException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidMapperException
     */
    protected function import(PriceImportTransfer $importTransfer): array
    {
        $this->priceImportTransfer = $importTransfer;

        try {
            $this
                ->updatePriceImportStatus(
                    DstPriceImportTableMap::COL_STATUS_RUNNING
                );

            $this
                ->openFile();

            $this
                ->setActiveMerchantPrices();

            $this
                ->findAssignedMapper();

            $readFile = $this
                ->readFile();

            if (!empty($readFile)) {
                $this->mailDeactivatedProductImport($readFile);
            }

            $this
                ->deleteRemainingPrices();
        } catch (Exception $exception) {
            $this
                ->updatePriceImportStatus(
                    DstPriceImportTableMap::COL_STATUS_FAILED
                );

            throw $exception;
        }

        $this
            ->updatePriceImportStatus(
                DstPriceImportTableMap::COL_STATUS_SENDING
            );

        $mailSuccess = $this
            ->mailPriceImport();

        if ($mailSuccess !== true) {
            $this
                ->updatePriceImportStatus(
                    DstPriceImportTableMap::COL_STATUS_FAILED
                );

            return [];
        }

        $this
            ->updatePriceImportStatus(
                DstPriceImportTableMap::COL_STATUS_DONE
            );

        return [
            static::IMPORT_RETURN_KEY_UPDATED => $this->cntUpdated,
            static::IMPORT_RETURN_KEY_IGNORED => $this->cntIgnored,
            static::IMPORT_RETURN_KEY_DELETED => $this->cntDeleted,
            static::IMPORT_RETURN_KEY_CREATED => $this->cntCreated
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return string
     */
    protected function getCsvPath(PriceImportTransfer $importTransfer): string
    {
        return sprintf(
            '%s/%d/%s',
            $this->config->getPriceImportFolder(),
            $importTransfer->getFkBranch(),
            $importTransfer->getCsvFile()
        );
    }

    /**
     * @return void
     */
    protected function setActiveMerchantPrices(): void
    {
        $prices = $this
            ->merchantPriceFacade
            ->getPricesForBranch(
                $this
                    ->priceImportTransfer
                    ->getFkBranch()
            );

        foreach ($prices as $price) {
            $this->merchantPrices[$price->getMerchantSku()] = $price->getIdPrice();
        }
    }

    /**
     * @return void
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportCouldNotOpenCsvException
     */
    protected function openFile(): void
    {
        $csvPath = $this
            ->getCsvPath(
                $this
                    ->priceImportTransfer
            );

        try {
            $this->file = new SplFileObject(
                $csvPath,
                'r'
            );
        } catch (Exception $exception) {
            throw new PriceImportCouldNotOpenCsvException(
                sprintf(
                    PriceImportCouldNotOpenCsvException::MESSAGE,
                    $this
                        ->priceImportTransfer
                        ->getCsvFile()
                )
            );
        }
    }

    /**
     * @return void
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidMapperException
     */
    protected function findAssignedMapper(): void
    {
        $mapperName = $this
            ->priceImportTransfer
            ->getMappingType();

        foreach ($this->mappers as $mapper) {
            if ($mapper->getName() === $mapperName) {
                $this->assignedMapper = $mapper;

                return;
            }
        }

        throw new PriceImportInvalidMapperException(
            sprintf(
                PriceImportInvalidMapperException::MESSAGE,
                substr($mapperName, strrpos($mapperName, '\\') + 1)
            )
        );
    }

    /**
     * @param array $row
     * @return bool
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidHeaderException
     */
    protected function checkHeaderLine(array $row): bool
    {
        $bom = pack(
            "CCC",
            0xef,
            0xbb,
            0xbf
        );

        if (0 === strncmp($row[0], $bom, 3)) {
            $row[0] = substr($row[0], 3);
        }

        if ($row !== $this->assignedMapper->getCsvHeaderFields()) {
            throw new PriceImportInvalidHeaderException(
                PriceImportInvalidHeaderException::MESSAGE
            );
        }

        return true;
    }

    /**
     * @return array
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidHeaderException
     */
    protected function readFile()
    {
        $array = [];
        while ($this->file->eof() !== true) {
            $row = $this
                ->file
                ->fgetcsv(
                    $this
                        ->assignedMapper
                        ->getImportDelimiter()
                );

            if ($this->isHeaderRow === true) {
                $this
                    ->checkHeaderLine(
                        $row
                    );

                $this
                    ->isHeaderRow = false;

                continue;
            }

            if ($this->isColumnCountTooLow($row) === true) {
                continue;
            }

            if ($this->isMerchantSkuSet($row) !== true
                || $this->isGrossPriceSet($row) !== true
            ) {
                $this
                    ->cntIgnored++;

                continue;
            }

            $updatePrice = $this
                ->createOrUpdatePrice($row);

            if ($updatePrice) {
                $array[] = $row;
            }
        }

        return $array;
    }

    /**
     * @param array $csvRow
     * @return bool
     */
    protected function isColumnCountTooLow(array $csvRow): bool
    {
        return (
            count($csvRow) < count($this->assignedMapper->getCsvHeaderFields())
        );
    }

    /**
     * @param array $csvRow
     * @return bool
     */
    protected function isMerchantSkuSet(array $csvRow): bool
    {
        $merchantSkuField = $this
            ->assignedMapper
            ->getMerchantSkuIndex();

        return (
            isset($csvRow[$merchantSkuField]) &&
            strlen(trim($csvRow[$merchantSkuField])) > 0
        );
    }

    /**
     * @param array $csvRow
     * @return bool
     */
    protected function isGrossPriceSet(array $csvRow): bool
    {
        $grossPriceField = $this
            ->assignedMapper
            ->getGrossPriceIndex();

        return (
            isset($csvRow[$grossPriceField]) &&
            strlen(trim($csvRow[$grossPriceField])) > 0
        );
    }

    /**
     * @param array $csvRow
     * @return bool
     */
    protected function doesMerchantPriceExist(array $csvRow): bool
    {
        $merchantSkuField = $this
            ->assignedMapper
            ->getMerchantSkuIndex();

        return array_key_exists(
            $csvRow[$merchantSkuField],
            $this->merchantPrices
        );
    }

    /**
     * @param array $csvRow
     * @return void | bool
     */
    protected function createOrUpdatePrice(array $csvRow)
    {
        $this
            ->cntCreated++;

        if ($this->doesMerchantPriceExist($csvRow) === true) {
            $this
                ->cntCreated--;

            $this
                ->cntUpdated++;

            $merchantSkuField = $this
                ->assignedMapper
                ->getMerchantSkuIndex();

            unset($this->merchantPrices[$csvRow[$merchantSkuField]]);
        }

        $priceTransfer = $this
            ->createPriceTransfer(
                $this
                    ->assignedMapper
                    ->getMappedRow($csvRow)
            );

        if (!$priceTransfer) {
            return false;
        }

        $this
            ->merchantPriceFacade
            ->importPriceForBranch(
                $priceTransfer
            );
    }

    /**
     * @param array $mappedCsv
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     */
    protected function createPriceTransfer(array $mappedCsv)
    {
        $priceTransfer = new PriceTransfer();

        $productEntity = $this
            ->productQueryContainer
            ->queryProduct()
            ->filterByIsActive(true)
            ->filterBySku($mappedCsv[static::IMPORT_KEY_SKU_POSITION])
            ->findOne();

        if (!$productEntity) {
            return false;
        } else {
            $productConcreteTransfer = $this
            ->getProductFromSku(
                $mappedCsv[static::IMPORT_KEY_SKU_POSITION]
            );

            if (!$productConcreteTransfer) {
                return false;
            }

            $priceTransfer
                ->setFkBranch(
                    $this
                        ->priceImportTransfer
                        ->getFkBranch()
                )
                ->setSku(
                    sprintf(
                        '%s_%d',
                        $mappedCsv[static::IMPORT_KEY_SKU_POSITION],
                        $this
                            ->priceImportTransfer
                            ->getFkBranch()
                    )
                )
                ->setMerchantSku(
                    $mappedCsv[static::IMPORT_KEY_MERCHANT_SKU_POSITION]
                )
                ->setProduct(
                    $productConcreteTransfer
                )
                ->setFkProduct(
                    $productConcreteTransfer
                        ->getIdProductConcrete()
                )
                ->setIsActive(
                    $mappedCsv[static::IMPORT_KEY_STATUS_POSITION]
                )
                ->setStatus(
                    $mappedCsv[static::IMPORT_KEY_STATUS_POSITION]
                )
                ->setPriceMode(
                    MerchantPriceConstants::PRICE_MODE_GROSS_NAME
                )->setGrossPrice(
                    $mappedCsv[static::IMPORT_KEY_PRICE_GROSS_POSITION] ?? 0.00
                );

            return $priceTransfer;
        }
    }

    /**
     * @param string $sku
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    protected function getProductFromSku(string $sku): ProductConcreteTransfer
    {
        return $this
            ->productFacade
            ->getProductConcrete(
                $sku
            );
    }

    /**
     * @return void
     */
    protected function deleteRemainingPrices(): void
    {
        foreach ($this->merchantPrices as $merchantSku => $idPrice) {
            $deleteProduct = $this
                ->merchantPriceFacade
                ->removePriceFromBranch(
                    $idPrice,
                    $this
                        ->priceImportTransfer
                        ->getFkBranch()
                );

            if ($deleteProduct != false) {
                $this
                    ->cntDeleted++;

                $this
                    ->cntIgnored--;
            }
        }
    }

    /**
     * @param string $status
     * @return void
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportInvalidIdException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function updatePriceImportStatus(string $status): void
    {
        $priceImportEntity = $this
            ->queryContainer
            ->queryPriceImportById(
                $this
                    ->priceImportTransfer
                    ->getIdPriceImport()
            )
            ->findOne();

        if ($priceImportEntity === null) {
            throw new PriceImportInvalidIdException(
                sprintf(
                    PriceImportInvalidIdException::MESSAGE,
                    $this
                        ->priceImportTransfer
                        ->getIdPriceImport()
                )
            );
        }

        $priceImportEntity
            ->setStatus(
                $status
            )
            ->setCntCreated(
                $this
                    ->cntCreated
            )
            ->setCntDeleted(
                $this
                    ->cntDeleted
            )
            ->setCntIgnored(
                $this
                    ->cntIgnored
            )
            ->setCntUpdated(
                $this
                    ->cntUpdated
            )
            ->save();
    }

    /**
     * @return bool
     */
    protected function mailPriceImport(): bool
    {
        try {
            $mailTransfer = $this
                ->createMailTransfer();

            $this
                ->mailFacade
                ->handleMail(
                    $mailTransfer
                );
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function mailDeactivatedProductImport(array $deactivatedProducts): bool
    {
        try {
            $mailTransfer = $this
                ->createDeactivatedProductMailTransfer($deactivatedProducts);

            $this
                ->mailFacade
                ->handleMail(
                    $mailTransfer
                );
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createDeactivatedProductMailTransfer(array $deactivatedProducts): MailTransfer
    {
        $mailTransfer = new MailTransfer();
        $message = '';
        foreach ($deactivatedProducts as $key => $deactivatedProduct) {
            $message .= '<br>' . $key . '. ' . $deactivatedProduct[1] . ': ' . $deactivatedProduct[0] . ' <br>';
        }

        $mailTransfer
            ->setSubject(
                static::MAIL_SUBJECT_DEACTIVATED_PRODUCT
            )
            ->setMessage(static::MAIL_BODY_DEACTIVATED_PRODUCT . $message)
            ->setType(
                BatchPriceImportDeactivatedProductsMailTypePlugin::MAIL_TYPE
            )
            ->setRecipients(
                $this
                    ->getRecipients()
            );

        return $mailTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        $mailTransfer = new MailTransfer();

        $mailTransfer
            ->setSubject(
                static::MAIL_SUBJECT
            )
            ->setMessage(
                sprintf(
                    static::MAIL_BODY,
                    $this
                        ->priceImportTransfer
                        ->getCsvFile()
                )
            )
            ->setType(
                BatchPriceImportMailTypePlugin::MAIL_TYPE
            )
            ->setRecipients(
                $this
                    ->getRecipients()
            )
            ->setTermsOfService(
                sprintf(
                    static::MAIL_STATS,
                    $this->cntUpdated,
                    $this->cntIgnored,
                    $this->cntDeleted,
                    $this->cntCreated
                )
            );

        return $mailTransfer;
    }

    /**
     * @return \ArrayObject
     */
    protected function getRecipients(): ArrayObject
    {
        $recipients = new ArrayObject();

        $branch = $this
            ->merchantFacade
            ->getBranchById($this->priceImportTransfer->getFkBranch());

        $dispatcherEmail = $branch->getDispatcherEmail();

        $dispatcherRecipient = (new MailRecipientTransfer())
            ->setName(
                $dispatcherEmail
            )
            ->setEmail(
                $dispatcherEmail
            );

        $recipients
            ->append(
                $dispatcherRecipient
            );

        $userRecipient = new MailRecipientTransfer();

        $userRecipient
            ->setName(
                $this
                    ->priceImportTransfer
                    ->getRecipient()
            )
            ->setEmail(
                $this
                    ->priceImportTransfer
                    ->getRecipient()
            );

        $recipients
            ->append(
                $userRecipient
            );

        $recipients->append((new MailRecipientTransfer())->setEmail('developer@durst.shop')->setName('Durst Devs'));

        return $recipients;
    }
}
