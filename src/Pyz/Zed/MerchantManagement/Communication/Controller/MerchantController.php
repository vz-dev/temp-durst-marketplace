<?php

namespace Pyz\Zed\MerchantManagement\Communication\Controller;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Merchant\Business\Exception\MerchantExistsException;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class MerchantController extends AbstractController
{

    public const PARAM_ID_MERCHANT = 'id-merchant';
    public const MERCHANT_LISTING_URL = '/merchant-management/merchant';

    public const MESSAGE_ERROR_MERCHANT_EXISTS = 'Es existiert bereits ein Händleraccount mit dieser Email-Adresse oder PIN';

    /**
     * @return array
     * @throws ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $merchantsTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createMerchantTable();

        return [
            'merchants' => $merchantsTable->render(),
        ];
    }

    /**
     * @return JsonResponse
     * @throws ContainerKeyNotFoundException
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createMerchantTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantFormDataProvider();

        $merchantForm = $this->getFactory()
            ->createFormFactory()
            ->createMerchantForm(
                $dataProvider->getData(null),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($merchantForm->isSubmitted() && $merchantForm->isValid()) {
            $merchantTransfer = $this
                ->getDataFromMerchantForm($merchantForm);

            try {
                $merchantTransfer = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->createMerchant($merchantTransfer);
            } catch (MerchantExistsException $e) {
                $this
                    ->addErrorMessage(static::MESSAGE_ERROR_MERCHANT_EXISTS);

                return $this->redirectResponse(self::MERCHANT_LISTING_URL);
            }

            if ($merchantTransfer->getIdMerchant()) {

                $this->addSuccessMessage(
                    sprintf('Merchant with id "%d" created', $merchantTransfer->getIdMerchant())
                );

                return $this->redirectResponse(self::MERCHANT_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new merchant!');
        }

        return $this->viewResponse([
            'merchantForm' => $merchantForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @throws ContainerKeyNotFoundException
     */
    public function updateAction(Request $request)
    {
        $idMerchant = $this->castId($request->get(self::PARAM_ID_MERCHANT));

        if (empty($idMerchant)) {
            $this->addErrorMessage('Missing merchant id!');

            return $this->redirectResponse(self::MERCHANT_LISTING_URL);
        }

        $dataProvider = $this
            ->getFactory()
            ->createFormFactory()
            ->createMerchantUpdateFormDataProvider();

        $merchantForm = $this->getFactory()
            ->createFormFactory()
            ->createUpdateMerchantForm(
                $dataProvider->getData($idMerchant),
                $dataProvider->getOptions()
            )
            ->handleRequest($request);

        if ($merchantForm->isSubmitted() && $merchantForm->isValid()) {
            $merchantTransfer = $this->getDataFromMerchantForm($merchantForm);

            $this
                ->getFactory()
                ->getMerchantFacade()
                ->updateMerchant($merchantTransfer);

            $this->addSuccessMessage('Merchant updated.');

            return $this->redirectResponse(self::MERCHANT_LISTING_URL);
        }

        return $this->viewResponse([
            'merchantForm' => $merchantForm->createView(),
            'idMerchant' => $idMerchant,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws ContainerKeyNotFoundException
     */
    public function activateMerchantAction(Request $request)
    {
        $idMerchant = $this->castId($request->get(self::PARAM_ID_MERCHANT));

        if (empty($idMerchant)) {
            $this->addErrorMessage('Missing merchant id!');

            return $this->redirectResponse(self::MERCHANT_LISTING_URL);
        }

        $updateStatus = $this
            ->getFactory()
            ->getMerchantFacade()
            ->activateMerchant($idMerchant);

        if ($updateStatus) {
            $this->addSuccessMessage(sprintf('Merchant with id "%d" successfully activated.', $idMerchant));
        } else {
            $this->addErrorMessage(sprintf('Failed to activate merchant with id "%d".', $idMerchant));
        }

        return $this->redirectResponse(self::MERCHANT_LISTING_URL);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws ContainerKeyNotFoundException
     */
    public function deactivateMerchantAction(Request $request)
    {
        $idMerchant = $this->castId($request->get(self::PARAM_ID_MERCHANT));

        if (empty($idMerchant)) {
            $this->addErrorMessage('Missing merchant id!');

            return $this->redirectResponse(self::MERCHANT_LISTING_URL);
        }

        $updateStatus = $this
            ->getFactory()
            ->getMerchantFacade()
            ->deactivateMerchant($idMerchant);

        if ($updateStatus) {
            $this->addSuccessMessage(sprintf('Merchant with id "%d" successfully deactivated.', $idMerchant));
        } else {
            $this->addErrorMessage(sprintf('Failed to deactivate merchant with id "%d".', $idMerchant));
        }

        return $this->redirectResponse(self::MERCHANT_LISTING_URL);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws MethodNotAllowedHttpException
     *
     * @throws ContainerKeyNotFoundException
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_DELETE)) {
            throw new MethodNotAllowedHttpException([Request::METHOD_DELETE], 'This action requires a DELETE request.');
        }

        $idMerchant = $this->castId($request->request->get(self::PARAM_ID_MERCHANT));

        if (empty($idMerchant)) {
            $this->addErrorMessage('Missing merchant id!');

            return $this->redirectResponse(self::MERCHANT_LISTING_URL);
        }

        $merchantTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->removeMerchant($idMerchant);

        if ($merchantTransfer->getStatus() === SpyMerchantTableMap::COL_STATUS_DELETED) {
            $this->addSuccessMessage(sprintf('Merchant with id "%d" successfully deleted.', $idMerchant));
        } else {
            $this->addErrorMessage(sprintf('Failed to delete merchant with id "%d".', $idMerchant));
        }

        return $this->redirectResponse(self::MERCHANT_LISTING_URL);
    }

    /**
     * @param FormInterface $form
     * @return MerchantTransfer
     */
    protected function getDataFromMerchantForm(FormInterface $form) : MerchantTransfer
    {
        return $form->getData();
    }
}
