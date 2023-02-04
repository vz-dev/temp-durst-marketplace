<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 25.10.17
 * Time: 12:55
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Generated\Shared\Transfer\DepositTransfer;
use Pyz\Zed\MerchantManagement\Communication\Form\DepositForm;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\MerchantManagementConfig;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DepositController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 * @method MerchantManagementConfig getConfig()
 */
class DepositController extends AbstractController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        $depositTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createDepositTable();

        return $this->viewResponse([
            'deposits' => $depositTable->render(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createDepositTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createDepositForm([], [])
            ->handleRequest($request);

        if($form->isValid()){
            $formData = $form->getData();

            $depositTransfer = new DepositTransfer();
            $depositTransfer->setName($formData[DepositForm::FIELD_NAME]);
            $depositTransfer->setPrice($this->deformatPrice($formData[DepositForm::FIELD_PRICE]));

            $depositTransfer = $this
                ->getFactory()
                ->getDepositFacade()
                ->addDeposit($depositTransfer);

            if ($depositTransfer->getIdDeposit()) {

                $this->addSuccessMessage(
                    sprintf('Deposit with id "%d" created', $depositTransfer->getIdDeposit())
                );

                return $this->redirectResponse(MerchantManagementConfig::DEPOSIT_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new deposit!');
        }

        return $this->viewResponse([
           'depositForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request)
    {
        $idDeposit = $this->castId($request->get(MerchantManagementConfig::PARAM_ID_DEPOSIT));

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createDepositFormDataProvider();

        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createDepositForm($dataProvider->getData($idDeposit), [])
            ->handleRequest($request);

        if($form->isValid()){
            $formData = $form->getData();

            $depositTransfer = new DepositTransfer();
            $depositTransfer->setIdDeposit($idDeposit);
            $depositTransfer->setName($formData[DepositForm::FIELD_NAME]);
            $depositTransfer->setPrice($this->deformatPrice($formData[DepositForm::FIELD_PRICE]));

            $depositTransfer = $this
                ->getFactory()
                ->getDepositFacade()
                ->updateDeposit($depositTransfer);

            if ($depositTransfer->getIdDeposit()) {

                $this->addSuccessMessage(
                    sprintf('Deposit with id "%d" updated', $depositTransfer->getIdDeposit())
                );

                return $this->redirectResponse(MerchantManagementConfig::DEPOSIT_LISTING_URL);
            }

            $this->addErrorMessage(sprintf('Failed to update deposit with id %d!', $idDeposit));
        }

        return $this->viewResponse([
            'depositForm' => $form->createView(),
            'idDeposit' => $idDeposit,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $idDeposit = $this->castId($request->get(MerchantManagementConfig::PARAM_ID_DEPOSIT));

        $this
            ->getFactory()
            ->getDepositFacade()
            ->removeDeposit($idDeposit);

        if($this
            ->getFactory()
            ->getDepositFacade()
            ->hasDeposit($idDeposit)
        ){
            $this->addErrorMessage(sprintf('Failed to delete deposit with id %d!', $idDeposit));
        }else {
            $this->addSuccessMessage(sprintf('Deposit with id %d successfully deleted', $idDeposit));
        }

        return $this->redirectResponse(MerchantManagementConfig::DEPOSIT_LISTING_URL);
    }

    /**
     * @param $price
     * @return int|string
     */
    protected function deformatPrice($price)
    {
        if($price === null){
            return null;
        }

        return $this
            ->getFactory()
            ->getMoneyFacade()
            ->convertDecimalToInteger($price);
    }
}