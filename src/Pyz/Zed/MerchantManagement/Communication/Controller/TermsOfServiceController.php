<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 14:17
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\MerchantManagement\Communication\Form\TermsOfServiceForm;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\Communication\Table\TermsOfServiceTable;
use Pyz\Zed\TermsOfService\Business\Exception\TermsOfServiceNotFoundException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class TermsOfServiceController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class TermsOfServiceController extends AbstractController
{
    const TERMS_OF_SERVICE_LISTING_URL = '/merchant-management/terms-of-service';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $termsOfServiceTable = $this
            ->getFactory()
            ->createTableFactory()
            ->createTermsOfServiceTable();

        return [
            'termsOfService' => $termsOfServiceTable->render(),
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
            ->createTermsOfServiceTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function createAction(Request $request)
    {
        $form = $this->getFactory()
            ->createFormFactory()
            ->createTermsOfServiceForm()
            ->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();

            $termsOfServiceTransfer = new TermsOfServiceTransfer();
            $termsOfServiceTransfer->setName($formData[TermsOfServiceForm::FIELD_NAME]);
            $termsOfServiceTransfer->setHintText($formData[TermsOfServiceForm::FIELD_HINT_TEXT]);
            $termsOfServiceTransfer->setButtonText($formData[TermsOfServiceForm::FIELD_BUTTON_TEXT]);
            $termsOfServiceTransfer->setActiveUntil($formData[TermsOfServiceForm::FIELD_ACTIVE_UNTIL]);
            $termsOfServiceTransfer->setText($formData[TermsOfServiceForm::FIELD_TEXT]);

            try {
                $termsOfServiceTransfer = $this
                    ->getFactory()
                    ->getTermsOfServiceFacade()
                    ->add($termsOfServiceTransfer);
            } catch (PropelException $e) {
                $this->addErrorMessage($e->getMessage());
            }

            if ($termsOfServiceTransfer->getIdTermsOfService()) {

                $this->addSuccessMessage(
                    sprintf('Terms of Service with id "%d" created', $termsOfServiceTransfer->getIdTermsOfService())
                );

                return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new Terms of Service!');
        }

        return $this->viewResponse([
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AmbiguousComparisonException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function editAction(Request $request)
    {
        $idTermsOfService = $this->castId($request->get(TermsOfServiceTable::PARAM_ID_TERMS_OF_SERVICE));

        if (empty($idTermsOfService)) {
            $this->addErrorMessage('Missing Terms of Service id!');

            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        }

        try {
            $termsOfServiceTransfer = $this
                ->getFactory()
                ->getTermsOfServiceFacade()
                ->getTermsOfServiceById($idTermsOfService);
        } catch(TermsOfServiceNotFoundException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        } catch(AmbiguousComparisonException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        }

        $form = $this->getFactory()
            ->createFormFactory()
            ->createTermsOfServiceForm([
                    TermsOfServiceForm::FIELD_ID => $termsOfServiceTransfer->getIdTermsOfService(),
                    TermsOfServiceForm::FIELD_NAME => $termsOfServiceTransfer->getName(),
                    TermsOfServiceForm::FIELD_HINT_TEXT => $termsOfServiceTransfer->getHintText(),
                    TermsOfServiceForm::FIELD_BUTTON_TEXT => $termsOfServiceTransfer->getButtonText(),
                    TermsOfServiceForm::FIELD_ACTIVE_UNTIL => $termsOfServiceTransfer->getActiveUntil(),
                    TermsOfServiceForm::FIELD_TEXT => $termsOfServiceTransfer->getText(),
                ]
            )
            ->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $paymentMethodTransfer = new TermsOfServiceTransfer();
            $paymentMethodTransfer->setIdTermsOfService($formData[TermsOfServiceForm::FIELD_ID]);
            $paymentMethodTransfer->setText($formData[TermsOfServiceForm::FIELD_TEXT]);
            $paymentMethodTransfer->setActiveUntil($formData[TermsOfServiceForm::FIELD_ACTIVE_UNTIL]);
            $paymentMethodTransfer->setHintText($formData[TermsOfServiceForm::FIELD_HINT_TEXT]);
            $paymentMethodTransfer->setButtonText($formData[TermsOfServiceForm::FIELD_BUTTON_TEXT]);
            $paymentMethodTransfer->setName($formData[TermsOfServiceForm::FIELD_NAME]);

            try {
                $this
                    ->getFactory()
                    ->getTermsOfServiceFacade()
                    ->update($paymentMethodTransfer);
            } catch (PropelException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
            }

            $this->addSuccessMessage(sprintf('Terms of Service #%d updated.', $idTermsOfService));

            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'idTermsOfService' => $idTermsOfService,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_DELETE)) {
            throw new MethodNotAllowedHttpException([Request::METHOD_DELETE], 'This action requires a DELETE request.');
        }

        $idTermsOfService = $this->castId($request->request->get(TermsOfServiceTable::PARAM_ID_TERMS_OF_SERVICE));

        if (empty($idTermsOfService)) {
            $this->addErrorMessage('Missing Payment Method id!');

            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        }

        try {
            $this
                ->getFactory()
                ->getTermsOfServiceFacade()
                ->remove($idTermsOfService);
        } catch (PropelException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        } catch (TermsOfServiceNotFoundException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        } catch (AmbiguousComparisonException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
        }

        $this->addSuccessMessage(sprintf('Terms of Service with id "%d" successfully deleted.', $idTermsOfService));

        return $this->redirectResponse(self::TERMS_OF_SERVICE_LISTING_URL);
    }
}