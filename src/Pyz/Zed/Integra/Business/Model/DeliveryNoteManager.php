<?php
/**
 * Durst - project - DeliveryNoteManager.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 24.08.21
 * Time: 21:53
 */

namespace Pyz\Zed\Integra\Business\Model;

use Generated\Shared\Transfer\DepositSkuTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfOptionsTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

class DeliveryNoteManager implements DeliveryNoteManagerInterface
{
    protected const DELIVERY_NOTE_FILE_NAME = '%s.pdf';
    protected const DELIVERY_NOTE_PDF_NAME = 'Lieferschein %s.pdf';

    /**
     * @var \Pyz\Zed\Integra\IntegraConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Pdf\Business\PdfFacadeInterface
     */
    protected $pdfFacade;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQuery;

    /**
     * @var DepositSkuTransfer[]
     */
    protected $deposits;

    /**
     * DeliveryNoteManager constructor.
     *
     * @param IntegraConfig $config
     * @param PdfFacadeInterface $pdfFacade
     * @param SalesFacadeInterface $salesFacade
     * @param OmsFacadeInterface $omsFacade
     * @param Filesystem $fileSystem
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     */
    public function __construct(
        IntegraConfig $config,
        PdfFacadeInterface $pdfFacade,
        SalesFacadeInterface $salesFacade,
        OmsFacadeInterface $omsFacade,
        Filesystem $fileSystem,
        MerchantQueryContainerInterface $merchantQueryContainer
    ) {
        $this->config = $config;
        $this->pdfFacade = $pdfFacade;
        $this->salesFacade = $salesFacade;
        $this->omsFacade = $omsFacade;
        $this->fileSystem = $fileSystem;
        $this->merchantQuery = $merchantQueryContainer;
    }

    /**
     * @param int[] $idOrders
     * @param int $idBranch
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     */
    public function createDeliveryNotePdfs(
        array $idOrders,
        int $idBranch
    ): array {

        $pdfTransfers = [];

        $this->getDepositsForBranch($idBranch);

        foreach($idOrders as $idOrder){
            $pdfTransfer = $this
                ->createPdfTransfer($this->salesFacade->getDeflatedOrderByIdSalesOrder($idOrder));

            $pdfTransfers[] = $this
                ->pdfFacade
                ->createPdfFile($pdfTransfer);
        }


        return $pdfTransfers;
    }

    /**
     * @param string $orderRef
     *
     * @return string
     */
    protected function getPdfFileName(
        string $orderRef
    ): string {
        return sprintf(
            static::DELIVERY_NOTE_FILE_NAME,
            $orderRef
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\PdfTransfer
     */
    protected function createPdfTransfer(OrderTransfer $orderTransfer): PdfTransfer
    {
        $pdfTransfer = new PdfTransfer();
        $receiptRef = $this->getReceiptRef($orderTransfer->getIntegraReceiptNo(), $orderTransfer->getOrderReference());

        $pdfTransfer
            ->setTemplateVariables(
                [
                    'order' => $orderTransfer,
                    'deposits' => $this->deposits,
                    'paymentMethod' => $this->getPaymentMethod($orderTransfer->getIntegraPaymentMethod()),
                    'settings' => [
                        'pdfAssetsPath' => $this->config->getPdfAssetPath(),
                    ],
                    'splitExpensesRefundsReturnDeposits' => $this->omsFacade->getSplitExpensesRefundsReturnDepositsFromOrder($orderTransfer),
                ]
            )
            ->setTemplate(
                $this
                    ->config
                    ->getDeliveryNotePdfTemplate()
            )
            ->setFileName(
                $this->getPdfFileName(
                    $receiptRef
                )
            )
            ->setDirPath(
                $this
                    ->config
                    ->getPdfDeliveryNotePath()
            )
            ->setPdfName(
                sprintf(
                    static::DELIVERY_NOTE_FILE_NAME,
                    $receiptRef
                )
            )
            ->setDisplayName(
                sprintf(
                    static::DELIVERY_NOTE_FILE_NAME,
                    $receiptRef
                )
            )
            ->setOptions(
                $this
                    ->createPdfOptions()
            );

        return $pdfTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PdfOptionsTransfer
     */
    protected function createPdfOptions(): PdfOptionsTransfer
    {
        return (new PdfOptionsTransfer())
            ->setMode('')
            ->setFormat('A4')
            ->setDefaultFontSize(0)
            ->setDefaultFont('')
            ->setMarginLeft(5)
            ->setMarginRight(5)
            ->setMarginTop(20)
            ->setMarginBottom(30)
            ->setMarginHeader(0)
            ->setMarginFooter(0)
            ->setOrientation('P');
    }

    /**
     * @param int|null $paymentMethod
     * @return string
     */
    protected function getPaymentMethod(?int $paymentMethod) : string
    {
        $paymentMethods = $this->config->getGbzPaymentMap();
        if($paymentMethod !== null && array_key_exists($paymentMethod, $paymentMethods) === true)
        {
            return $paymentMethods[$paymentMethod];
        }

        return 'Unbekannt';
    }

    /**
     * @param int $idBranch
     */
    protected function getDepositsForBranch(int $idBranch) : void
    {
        $spyDeposits = [];
        $deposits = $this
            ->merchantQuery
            ->queryBranchToDepositWithAcceptedDeposits($idBranch)
            ->joinWithSpyDeposit()
            ->find()
            ->toArray('FkDeposit');


        foreach($deposits as $idDeposit =>  $deposit)
        {
            $spyDeposits[$idDeposit] = $deposit['SpyDeposit'];
        }

        $this->deposits = $spyDeposits;
    }

    /**
     * @param string|null $receiptNo
     * @param string $orderRef
     * @return string
     */
    protected function getReceiptRef(?string $receiptNo, string $orderRef) : string
    {
        if($receiptNo !== null)
        {
            return sprintf('M%s', $receiptNo);
        }

        return $orderRef;
    }
}
