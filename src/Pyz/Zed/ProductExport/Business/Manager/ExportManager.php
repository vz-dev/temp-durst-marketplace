<?php
/**
 * Durst - project - ExportManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 09:54
 */

namespace Pyz\Zed\ProductExport\Business\Manager;

use ArrayObject;
use DateTime;
use Exception;
use Generated\Shared\Transfer\MailAttachmentTransfer;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\PriceTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Orm\Zed\Product\Persistence\Base\SpyProduct;
use Orm\Zed\ProductExport\Persistence\Base\DstProductExport;
use Orm\Zed\ProductExport\Persistence\Map\DstProductExportTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\ProductExport\Communication\Plugin\Mail\BatchProductExportMailTypePlugin;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMailBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToMerchantPriceBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Facade\ProductExportToProductBridgeInterface;
use Pyz\Zed\ProductExport\Dependency\Persistence\ProductExportToProductQueryContainerBridgeInterface;
use Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface;
use Pyz\Zed\ProductExport\ProductExportConfig;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;

class ExportManager implements ExportManagerInterface
{
    protected const FILE_NAME_PATTERN = 'product_export_%d_%s.csv';
    protected const DELIMITER = ';';

    protected const MAIL_SUBJECT = 'Produktexport %s';
    protected const MAIL_MESSAGE = 'Der Export der Produkte befindet sich im Anhang.';

    protected const DURST_SKU = 'durst_sku';
    protected const PRODUCT_NAME = 'product_name';
    protected const PRODUCT_UNIT = 'product_unit';
    protected const MERCHANT_SKU = 'merchant_sku';
    protected const PRICE_NET = 'price_netto';
    protected const PRICE_GROSS = 'price_brutto';
    protected const PRODUCT_STATUS = 'product_status';

    protected const KEY_ATTR_NAME = 'name';
    protected const KEY_ATTR_UNIT = 'unit';

    protected const NOT_AVAILABLE = 'N/A';

    /**
     * @var ProductExportQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var ProductExportToProductQueryContainerBridgeInterface
     */
    protected $productQueryContainer;

    /**
     * @var ProductExportToProductBridgeInterface
     */
    protected $productFacade;

    /**
     * @var ProductExportToMerchantPriceBridgeInterface
     */
    protected $merchantPriceFacade;

    /**
     * @var ProductExportToMailBridgeInterface
     */
    protected $mailFacade;

    /**
     * @var ProductExportConfig
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var SplFileObject
     */
    protected $file;

    /**
     * @var PriceTransfer[]
     */
    protected $merchantPrices = [];

    /**
     * @var array
     */
    protected $csvHeaders = [];

    /**
     * @var int
     */
    protected $totalProducts = 0;

    /**
     * @var int
     */
    protected $step = 0;

    /**
     * @var int
     */
    protected $pages = 0;

    /**
     * ExportManager constructor.
     *
     * @param ProductExportQueryContainerInterface $queryContainer
     * @param ProductExportToProductQueryContainerBridgeInterface $productQueryContainer
     * @param ProductExportToProductBridgeInterface $productFacade
     * @param ProductExportToMerchantPriceBridgeInterface $merchantPriceFacade
     * @param ProductExportToMailBridgeInterface $mailFacade
     * @param ProductExportConfig $config
     * @param Filesystem $filesystem
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        ProductExportQueryContainerInterface $queryContainer,
        ProductExportToProductQueryContainerBridgeInterface $productQueryContainer,
        ProductExportToProductBridgeInterface $productFacade,
        ProductExportToMerchantPriceBridgeInterface $merchantPriceFacade,
        ProductExportToMailBridgeInterface $mailFacade,
        ProductExportConfig $config,
        Filesystem $filesystem,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->productQueryContainer = $productQueryContainer;
        $this->productFacade = $productFacade;
        $this->merchantPriceFacade = $merchantPriceFacade;
        $this->mailFacade = $mailFacade;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function exportNext(): void
    {
        $nextExport = $this
            ->queryContainer
            ->queryProductExportWaiting()
            ->findOne();

        if ($nextExport === null) {
            return;
        }

        $this
            ->prepareHeaders();

        $this
            ->setTotalProductCount();

        $this
            ->calculatePages();

        $this
            ->setMerchantPrices(
                $nextExport
                    ->getFkBranch()
            );

        $nextExport = $this
            ->updateProductExport(
                $nextExport
            );

        $exported = $this
            ->saveCsvFile(
                $nextExport
            );

        if ($exported !== true) {
            $this
                ->updateExportState(
                    $nextExport,
                    DstProductExportTableMap::COL_STATUS_FAILED
                );

            return;
        }

        $mailed = $this
            ->mailProductExport(
                $nextExport
            );

        if ($mailed === true) {
            $this
                ->updateExportState(
                    $nextExport,
                    DstProductExportTableMap::COL_STATUS_DONE
                );

            return;
        }

        $this
            ->updateExportState(
                $nextExport,
                DstProductExportTableMap::COL_STATUS_FAILED
            );
    }

    /**
     *
     * @return ProductConcreteTransfer[]
     */
    protected function getNextProductBatch(): array
    {
        $batchSize = $this
            ->config
            ->getBatchSize();

        $productEntities = $this
            ->productQueryContainer
            ->queryProduct()
            ->filterByIsActive(
                true
            )
            ->setLimit($batchSize)
            ->setOffset(
                $this
                    ->getOffset()
            )
            ->orderBySku(
                Criteria::ASC
            )
            ->find();

        $products = [];

        foreach ($productEntities as $productEntity) {
            $products[] = $this
                ->convertProductEntityToTransfer(
                    $productEntity
                );
        }

        return $products;
    }

    /**
     * @param int $idBranch
     *
     * @return void
     */
    protected function setMerchantPrices(int $idBranch): void
    {
        $merchantPrices = $this
            ->merchantPriceFacade
            ->getPricesForBranch(
                $idBranch
            );

        foreach ($merchantPrices as $merchantPrice) {
            $this->merchantPrices[$merchantPrice->getFkProduct()] = $merchantPrice;
        }
    }

    /**
     *
     * @return void
     */
    protected function setTotalProductCount(): void
    {
        $this->totalProducts = $this
            ->productQueryContainer
            ->queryProduct()
            ->filterByIsActive(
                true
            )
            ->count();
    }

    /**
     * @return void
     */
    protected function calculatePages(): void
    {
        $this->pages = (int)ceil($this->totalProducts / $this->config->getBatchSize());
    }

    /**
     * @return int
     */
    protected function getOffset(): int
    {
        $offset = ($this->step * $this->config->getBatchSize());

        $this->step++;

        return $offset;
    }

    /**
     * @param ProductConcreteTransfer $productConcreteTransfer
     *
     * @return bool
     */
    protected function hasProductMerchantPrice(ProductConcreteTransfer $productConcreteTransfer): bool
    {
        return (array_key_exists($productConcreteTransfer->getIdProductConcrete(), $this->merchantPrices));
    }

    /**
     * @param int $idBranch
     *
     * @return string
     */
    protected function generateFilename(int $idBranch): string
    {
        $current = new DateTime('now');

        $filename = sprintf(
            static::FILE_NAME_PATTERN,
            $idBranch,
            $current->format('U')
        );

        return sprintf(
            '%s/%s',
            $this
                ->getFilepath(),
            $filename
        );
    }


    /**
     * @return string
     */
    protected function getFilepath(): string
    {
        if ($this->filesystem->exists($this->config->getFilePath()) !== true) {
            $this->filesystem->mkdir($this->config->getFilePath());
        }

        return $this->config->getFilePath();
    }

    /**
     * @param DstProductExport $productExport
     *
     * @return DstProductExport
     */
    protected function updateProductExport(DstProductExport $productExport): DstProductExport
    {
        $productExport
            ->setCntTotalProducts(
                $this
                    ->totalProducts
            )
            ->setCntMerchantProducts(
                count(
                    $this
                        ->merchantPrices
                )
            )
            ->setFileName(
                $this
                    ->generateFilename(
                        $productExport
                            ->getFkBranch()
                    )
            )
            ->setStatus(
                DstProductExportTableMap::COL_STATUS_RUNNING
            );

        $productExport
            ->save();

        return $productExport;
    }

    /**
     * @param DstProductExport $productExport
     * @param string $state
     *
     * @return DstProductExport
     */
    protected function updateExportState(
        DstProductExport $productExport,
        string $state
    ): DstProductExport {
        $productExport
            ->setStatus(
                $state
            )
            ->save();

        return $productExport;
    }

    /**
     * @param DstProductExport $productExport
     *
     * @return bool
     */
    protected function saveCsvFile(DstProductExport $productExport): bool
    {
        $this
            ->openFile(
                $productExport
                    ->getFileName()
            );

        try {
            $this
                ->file
                ->fputcsv(
                    $this
                        ->csvHeaders,
                    static::DELIMITER
                );

            while ($this->step < $this->pages) {
                $currentBatchProducts = $this
                    ->getNextProductBatch();

                foreach ($currentBatchProducts as $currentBatchProduct) {
                    $product = $this
                        ->transferToAssocArray(
                            $currentBatchProduct
                        );
                    if ($product === false) {
                        continue;
                    }

                    $this
                        ->file
                        ->fputcsv(
                            $product,
                            static::DELIMITER
                        );
                }
            }
        } finally {
            $writeResult = $this
                ->file
                ->fflush();
        }

        if ($writeResult === true) {
            $this
                ->updateExportState(
                    $productExport,
                    DstProductExportTableMap::COL_STATUS_SENDING
                );
        }

        return $writeResult;
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    protected function openFile(string $filePath): void
    {
        $this->file = new SplFileObject(
            $filePath,
            'w'
        );
    }

    /**
     * @return void
     */
    protected function prepareHeaders(): void
    {
        $this->csvHeaders = [
            static::DURST_SKU,
            static::PRODUCT_NAME,
            static::PRODUCT_UNIT,
            static::MERCHANT_SKU,
            static::PRICE_GROSS,
            static::PRODUCT_STATUS,
        ];
    }

    /**
     * @param ProductConcreteTransfer $productConcreteTransfer
     *
     * @return array| bool
     */
    protected function transferToAssocArray(ProductConcreteTransfer $productConcreteTransfer)
    {
        $merchantSku = null;
        $priceGross = null;
        $active = 0;

        if ($this->hasProductMerchantPrice($productConcreteTransfer) === true) {
            $price = $this
                ->merchantPrices[$productConcreteTransfer
                ->getIdProductConcrete()];

            $merchantSku = $price->getMerchantSku();
            $priceGross = $price->getGrossPrice();
            $active = $this->getStatusNumber($price->getStatus());
        }

        $attributes = $productConcreteTransfer
            ->getAttributes();

        return [
            $productConcreteTransfer->getSku(),
            $this->getProductName($attributes),
            $this->getProductUnit($attributes),
            $merchantSku,
            $priceGross,
            $active,
        ];
    }

    /**
     * @param string $active
     * @return int
     */
    private function getStatusNumber(string $active): int
    {
        if($active == 'active') {
            return 1;
        } else if ($active == 'inactive') {
            return 0;
        } else {
            return 2;
        }
    }

    /**
     * @param string $json
     *
     * @return string
     */
    protected function getProductName(string $json): string
    {
        $attributes = json_decode($json);

        if (isset($attributes->{static::KEY_ATTR_NAME}) === true) {
            return $attributes->{static::KEY_ATTR_NAME};
        }

        return static::NOT_AVAILABLE;
    }

    /**
     * @param string $json
     *
     * @return string
     */
    protected function getProductUnit(string $json): string
    {
        $attributes = json_decode($json);

        if (isset($attributes->{static::KEY_ATTR_UNIT}) === true) {
            return $attributes->{static::KEY_ATTR_UNIT};
        }

        return static::NOT_AVAILABLE;
    }

    /**
     * @param DstProductExport $productExport
     *
     * @return bool
     */
    protected function mailProductExport(DstProductExport $productExport): bool
    {
        try {
            $mailTransfer = $this
                ->createMailTransfer(
                    $productExport
                );

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
     * @param DstProductExport $productExport
     *
     * @return MailTransfer
     */
    protected function createMailTransfer(
        DstProductExport $productExport
    ): MailTransfer {
        $mailTransfer = new MailTransfer();

        $filename = $this
            ->getFilename();

        $mailTransfer
            ->setSubject(
                sprintf(
                    static::MAIL_SUBJECT,
                    $filename
                )
            )
            ->setMessage(
                static::MAIL_MESSAGE
            )
            ->setType(
                BatchProductExportMailTypePlugin::MAIL_TYPE
            )
            ->setRecipients(
                $this
                    ->getMailRecipients(
                        $productExport
                    )
            )
            ->addAttachment(
                $this
                    ->createMailAttachment()
            );

        return $mailTransfer;
    }

    /**
     * @param DstProductExport $productExport
     *
     * @return ArrayObject
     */
    protected function getMailRecipients(
        DstProductExport $productExport
    ): ArrayObject {
        $recipients = new ArrayObject();

        $userRecipient = new MailRecipientTransfer();

        $userRecipient
            ->setName(
                $productExport
                    ->getRecipient()
            )
            ->setEmail(
                $productExport
                    ->getRecipient()
            );

        $recipients
            ->append(
                $userRecipient
            );

        if($productExport->getRecipientCc() !== null){
            $ccRecipient = new MailRecipientTransfer();

            $ccRecipient
                ->setName(
                    $productExport
                        ->getRecipientCc()
                )
                ->setEmail(
                    $productExport
                        ->getRecipientCc()
                );
            $recipients
                ->append($ccRecipient);
        }

        return $recipients;
    }

    /**
     * @return MailAttachmentTransfer
     */
    protected function createMailAttachment(): MailAttachmentTransfer
    {
        $filename = $this
            ->getFilename();

        return (new MailAttachmentTransfer())
            ->setAttachmentUrl(
                $this
                    ->file
                    ->getRealPath()
            )
            ->setDisplayName(
                $filename
            )
            ->setFileName(
                $this
                    ->file
                    ->getFilename()
            );
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        return pathinfo(
            $this
                ->file
                ->getBasename(),
            PATHINFO_FILENAME
        );
    }

    /**
     * @param SpyProduct $product
     *
     * @return ProductConcreteTransfer
     */
    protected function convertProductEntityToTransfer(SpyProduct $product): ProductConcreteTransfer
    {
        $transfer = (new ProductConcreteTransfer())
            ->fromArray(
                $product
                    ->toArray(),
                true
            );

        $transfer
            ->setIdProductConcrete(
                $product
                    ->getIdProduct()
            );

        return $transfer;
    }
}
