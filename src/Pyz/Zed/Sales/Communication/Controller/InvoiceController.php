<?php
/**
 * Durst - project - InvoiceController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-25
 * Time: 09:24
 */

namespace Pyz\Zed\Sales\Communication\Controller;


use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceSepaMailTypePlugin;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Communication\Exception\NoInvoiceForOrderException;
use Pyz\Zed\Sales\Communication\SalesCommunicationFactory;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Sales\SalesConfig;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method SalesCommunicationFactory getFactory()
 * @method SalesFacadeInterface getFacade()
 * @method SalesQueryContainerInterface getQueryContainer()
 */
class InvoiceController extends AbstractController
{
    public const SEPA_PAYMENT_TYPES = ['HeidelpayRestSepaDirectDebit', 'HeidelpayRestSepaDirectDebitGuaranteed', 'HeidelpayRestSepaDirectDebitB2B'];


    public function resendAction(Request $request)
    {
        $idSalesOrder = $this->castId($request->query->getInt(SalesConfig::PARAM_ID_SALES_ORDER));


        $orderTransfer = $this
            ->getFacade()
            ->getDeflatedOrderByIdSalesOrder($idSalesOrder);

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($orderTransfer->getFkBranch());

        $this
            ->getFactory()
            ->getOmsFacade()
            ->sendInvoiceMail($orderTransfer, $branchTransfer, $this->getInvoiceMailTemplateByPaymentMethod($orderTransfer->getPayments()[0]->getPaymentMethod()));


        return RedirectResponse::create('/sales');
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws NoInvoiceForOrderException
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     */
    public function viewAction(Request $request)
    {
        $idSalesOrder = $this->castId($request->query->getInt(SalesConfig::PARAM_ID_SALES_ORDER));

        $orderTransfer = $this
            ->getFacade()
            ->getDeflatedOrderByIdSalesOrder($idSalesOrder);

        $invoiceReference = $orderTransfer->getInvoiceReference();

        if ($invoiceReference === null) {
            throw new NoInvoiceForOrderException(
                sprintf(NoInvoiceForOrderException::MESSAGE, $orderTransfer->getFkBranch())
            );
        }

        $invoicePdfFilePath = $this
            ->getFactory()
            ->getInvoiceFacade()
            ->getInvoicePdfFilePathForOrder($invoiceReference, $orderTransfer->getFkBranch());

        return BinaryFileResponse::create(
            $invoicePdfFilePath,
            Response::HTTP_OK,
            ['Content-Disposition' => sprintf('inline; filename="%s"', basename($invoicePdfFilePath))]
        );
    }

    /**
     * @param string $paymentMethod
     * @return string
     */
    protected function getInvoiceMailTemplateByPaymentMethod(string $paymentMethod) : string
    {
        if(in_array($paymentMethod, self::SEPA_PAYMENT_TYPES) === true)
        {
            return MerchantOrderInvoiceSepaMailTypePlugin::MAIL_TYPE;
        }

        return MerchantOrderInvoiceMailTypePlugin::MAIL_TYPE;
    }
}
