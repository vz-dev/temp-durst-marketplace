<?php
/**
 * Durst - project - EditController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.12.19
 * Time: 11:37
 */

namespace Pyz\Zed\Sales\Communication\Controller;

use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Sales\Communication\Controller\EditController as SprykerEditController;
use Spryker\Zed\Sales\SalesConfig;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Zed\Sales\Communication\SalesCommunicationFactory getFactory()
 * @method \Pyz\Zed\Sales\Business\SalesFacadeInterface getFacade()
 */
class EditController extends SprykerEditController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function addressAction(Request $request)
    {
        $idSalesOrder = $this->castId($request->query->get(SalesConfig::PARAM_ID_SALES_ORDER));
        $idOrderAddress = $this->castId($request->query->get('id-address'));

        $dataProvider = $this->getFactory()->createAddressFormDataProvider();
        $form = $this->getFactory()
            ->getAddressForm(
                $dataProvider->getData($idOrderAddress),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressTransfer = (new AddressTransfer())->fromArray($form->getData(), true);
            $addressTransfer->setIdSalesOrderAddress($idOrderAddress);
            $this->getFacade()
                ->updateOrderAddress($addressTransfer, $idOrderAddress);

            $this->addSuccessMessage('Address successfully updated.');

            return $this->redirectResponse(
                Url::generate(
                    '/sales/detail',
                    [
                        SalesConfig::PARAM_ID_SALES_ORDER => $idSalesOrder,
                    ]
                )->build()
            );
        }

        $orderTransfer = $this
            ->getFacade()
            ->getOrderByIdSalesOrder($idSalesOrder);

        $invoiceCreated = ($orderTransfer->getInvoiceReference() !== null);

        return $this->viewResponse([
            'idSalesOrder' => $idSalesOrder,
            'invoiceCreated' => $invoiceCreated,
            'form' => $form->createView(),
        ]);
    }
}
