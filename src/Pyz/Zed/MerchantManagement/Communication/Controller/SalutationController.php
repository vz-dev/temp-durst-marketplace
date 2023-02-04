<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.01.18
 * Time: 14:09
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Generated\Shared\Transfer\SalutationTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Merchant\Business\Exception\SalutationIdNotSetException;
use Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException;
use Pyz\Zed\MerchantManagement\Communication\Form\SalutationForm;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\Communication\Table\SalutationTable;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class SalutationController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class SalutationController extends AbstractController
{
    const SALUTATION_LISTING_URL = '/merchant-management/salutation';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createSalutationTable();

        return [
            'salutations' => $table->render(),
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
            ->createSalutationTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAction(Request $request)
    {
        $salutationForm = $this->getFactory()
            ->createFormFactory()
            ->createSalutationForm()
            ->handleRequest($request);

        if ($salutationForm->isValid()) {
            $formData = $salutationForm->getData();

            $salutation = new SalutationTransfer();
            $salutation->setName($formData[SalutationForm::FIELD_NAME]);

            $salutation = $this
                ->getFactory()
                ->getMerchantFacade()
                ->addSalutation($salutation);

            if ($salutation->getIdSalutation()) {

                $this->addSuccessMessage(
                    sprintf('Salutation with id "%d" created', $salutation->getIdSalutation())
                );

                return $this->redirectResponse(self::SALUTATION_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new salutation!');
        }

        return $this->viewResponse([
            'form' => $salutationForm->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function editAction(Request $request)
    {
        $idSalutation = $this->castId($request->get(SalutationTable::PARAM_ID_SALUTATION));

        if (empty($idSalutation)) {
            $this->addErrorMessage('Missing salutation id!');

            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        }

        try {
            $paymentMethod = $this
                ->getFactory()
                ->getMerchantFacade()
                ->getSalutationById($idSalutation);
        } catch(SalutationNotFoundException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        } catch(AmbiguousComparisonException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        }

        $form = $this->getFactory()
            ->createFormFactory()
            ->createSalutationForm([
                    SalutationForm::FIELD_ID => $paymentMethod->getIdSalutation(),
                    SalutationForm::FIELD_NAME => $paymentMethod->getName(),
                ]
            )
            ->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $salutationTransfer = new SalutationTransfer();
            $salutationTransfer->setIdSalutation($formData[SalutationForm::FIELD_ID]);
            $salutationTransfer->setName($formData[SalutationForm::FIELD_NAME]);

            try {
                $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->updateSalutation($salutationTransfer);
            } catch (PropelException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::SALUTATION_LISTING_URL);
            } catch (SalutationIdNotSetException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::SALUTATION_LISTING_URL);
            } catch (SalutationNotFoundException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::SALUTATION_LISTING_URL);
            } catch (AmbiguousComparisonException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::SALUTATION_LISTING_URL);
            }

            $this->addSuccessMessage(sprintf('Salutation #%d updated.', $idSalutation));

            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'idSalutation' => $idSalutation,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_DELETE)) {
            throw new MethodNotAllowedHttpException([Request::METHOD_DELETE], 'This action requires a DELETE request.');
        }

        $idSalutation = $this->castId($request->request->get(SalutationTable::PARAM_ID_SALUTATION));

        if (empty($idSalutation)) {
            $this->addErrorMessage('Missing Salutation id!');

            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        }

        try {
            $this
                ->getFactory()
                ->getMerchantFacade()
                ->removeSalutation($idSalutation);
        } catch (PropelException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        } catch (SalutationNotFoundException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        } catch (AmbiguousComparisonException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::SALUTATION_LISTING_URL);
        }

        $this->addSuccessMessage(sprintf('Salutation with id "%d" successfully deleted.', $idSalutation));

        return $this->redirectResponse(self::SALUTATION_LISTING_URL);
    }
}
