<?php

namespace Pyz\Zed\MerchantManagement\Communication\Controller;

use Pyz\Zed\Merchant\Business\Exception\Code\CodeNotValidException;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class BranchController extends AbstractController
{
    const BRANCH_LISTING_URL = '/merchant-management/branch';
    const PARAM_ID_BRANCH = 'id-branch';

    const MESSAGE_BRANCH_ADDED = 'Branch with id %d successfully created';
    const MESSAGE_BRANCH_UPDATED = 'Branch with id %d successfully updated';
    const MESSAGE_BRANCH_REMOVED = 'Branch with id %d successfully removed';
    const MESSAGE_BRANCH_RESTORED = 'Branch with id %d successfully restored';
    const MESSAGE_FORM_INVALID = 'Please check your input. The Form is not valid';
    const MESSAGE_ERROR_BRANCH_ADD = 'An error occurred. Branch could not be added';
    const MESSAGE_ERROR_BRANCH_UPDATE = 'An error occurred. Branch could not be updated';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $branchesTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createBranchTable();

        return [
            'branches' => $branchesTable->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createBranchTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deleteAction(Request $request)
    {
        $idBranch = $request->get(self::PARAM_ID_BRANCH);

        $this
            ->getFactory()
            ->getMerchantFacade()
            ->removeBranch($idBranch);

        $this
            ->addSuccessMessage(
                sprintf(
                    self::MESSAGE_BRANCH_REMOVED,
                    $idBranch
                )
            );

        return $this
            ->redirectResponse(self::BRANCH_LISTING_URL);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function restoreAction(Request $request)
    {
        $idBranch = $request->get(self::PARAM_ID_BRANCH);

        $this
            ->getFactory()
            ->getMerchantFacade()
            ->restoreBranch($idBranch);

        $this
            ->addSuccessMessage(
                sprintf(
                    self::MESSAGE_BRANCH_RESTORED,
                    $idBranch
                )
            );

        return $this
            ->redirectResponse(self::BRANCH_LISTING_URL);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Billing\Business\Exception\BranchNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchFormDataProvider();

        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchForm($dataProvider->getOptions())
            ->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->isValid()){
                $branchTransfer = $form->getData();

                try {
                    $branchTransfer = $this
                        ->getFactory()
                        ->getMerchantFacade()
                        ->addBranch($branchTransfer);
                }catch(CodeNotValidException $e){
                    $this->addErrorMessage($e->getMessage());
                }

                if($branchTransfer->getIdBranch() !== null){
                    $this
                        ->addSuccessMessage(
                            sprintf(
                                self::MESSAGE_BRANCH_ADDED,
                                $branchTransfer->getIdBranch()
                            )
                        );

                    return $this
                        ->redirectResponse(self::BRANCH_LISTING_URL);
                }

                $this
                    ->addErrorMessage(self::MESSAGE_ERROR_BRANCH_ADD);

            }else{
                $this
                    ->addErrorMessage(self::MESSAGE_FORM_INVALID);
            }
        }


        return $this
            ->viewResponse([
                'branchForm' => $form->createView(),
            ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updateAction(Request $request)
    {
        $idBranch = $request->get(self::PARAM_ID_BRANCH);

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($idBranch);

        $paymentMethodIds = $branchTransfer->getPaymentMethodIds();

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchFormDataProvider();

        $form = $this
            ->getFactory()
            ->createFormFactory()
            ->createBranchUpdateForm($dataProvider->getData($idBranch), $dataProvider->getOptions($branchTransfer->getFkMerchant()))
            ->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->isValid()){
                $branchTransfer = $form->getData();

                $paymentMethodIdsToDelete = array_diff($paymentMethodIds, array_merge($branchTransfer->getB2cPaymentMethodIds(), $branchTransfer->getB2bPaymentMethodIds()));

                foreach($paymentMethodIdsToDelete as $id){
                    $this
                        ->getFactory()
                        ->getMerchantFacade()
                        ->removePaymentMethodFromBranch($id, $branchTransfer->getIdBranch());
                }

                $branchTransfer = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->updateBranch($branchTransfer);

                if($branchTransfer->getIdBranch() !== null){
                    $this
                        ->addSuccessMessage(
                            sprintf(
                                self::MESSAGE_BRANCH_UPDATED,
                                $branchTransfer->getIdBranch()
                            )
                        );

                    return $this
                        ->redirectResponse(self::BRANCH_LISTING_URL);
                }

                $this
                    ->addErrorMessage(self::MESSAGE_ERROR_BRANCH_UPDATE);

            }else{
                $this
                    ->addErrorMessage(self::MESSAGE_FORM_INVALID);
            }
        }

        return $this
            ->viewResponse([
                'branchForm' => $form->createView(),
                'idBranch' => $idBranch,
            ]);
    }
}
