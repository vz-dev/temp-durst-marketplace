<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 04.01.18
 * Time: 09:48
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Generated\Shared\Transfer\PaymentMethodTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodExistsException;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException;
use Pyz\Zed\MerchantManagement\Communication\Form\PaymentMethodForm;
use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\Communication\Table\PaymentMethodsTable;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class PaymentMethodController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class PaymentMethodController extends AbstractController
{
    const PAYMENT_METHOD_LISTING_URL = '/merchant-management/payment-method';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createPaymentMethodsTable();

        return [
            'paymentMethods' => $table->render(),
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
            ->createPaymentMethodsTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAction(Request $request)
    {
        $paymentMethodForm = $this->getFactory()
            ->createFormFactory()
            ->createPaymentMethodForm()
            ->handleRequest($request);

        if ($paymentMethodForm->isValid()) {
            $formData = $paymentMethodForm->getData();

            $paymentMethod = new PaymentMethodTransfer();
            $paymentMethod->setName($formData[PaymentMethodForm::FIELD_NAME]);
            $paymentMethod->setCode($formData[PaymentMethodForm::FIELD_CODE]);

            try {
                $paymentMethod = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->addPaymentMethod($paymentMethod);
            } catch (PropelException $e) {
                $this->addErrorMessage($e->getMessage());
            } catch (PaymentMethodExistsException $e) {
                $this->addErrorMessage($e->getMessage());
            }

            if ($paymentMethod->getIdPaymentMethod()) {

                $this->addSuccessMessage(
                    sprintf('Payment Method with id "%d" created', $paymentMethod->getIdPaymentMethod())
                );

                return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
            }

            $this->addErrorMessage('Failed to create new payment method!');
        }

        return $this->viewResponse([
            'form' => $paymentMethodForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function editAction(Request $request)
    {
        $idPaymentMethod = $this->castId($request->get(PaymentMethodsTable::PARAM_ID_PAYMENT_METHOD));

        if (empty($idPaymentMethod)) {
            $this->addErrorMessage('Missing payment method id!');

            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        }

        try {
            $paymentMethod = $this
                ->getFactory()
                ->getMerchantFacade()
                ->getPaymentMethodById($idPaymentMethod);
        } catch(PaymentMethodNotFoundException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        } catch(AmbiguousComparisonException $e){
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        }

        $form = $this->getFactory()
            ->createFormFactory()
            ->createPaymentMethodForm([
                    PaymentMethodForm::FIELD_ID => $paymentMethod->getIdPaymentMethod(),
                    PaymentMethodForm::FIELD_NAME => $paymentMethod->getName(),
                    PaymentMethodForm::FIELD_CODE => $paymentMethod->getCode(),
                ]
            )
            ->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $paymentMethodTransfer = new PaymentMethodTransfer();
            $paymentMethodTransfer->setIdPaymentMethod($formData[PaymentMethodForm::FIELD_ID]);
            $paymentMethodTransfer->setName($formData[PaymentMethodForm::FIELD_NAME]);
            $paymentMethodTransfer->setCode($formData[PaymentMethodForm::FIELD_CODE]);

            try {
                $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->updatePaymentMethod($paymentMethodTransfer);
            } catch (PropelException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
            } catch (PaymentMethodNotFoundException $e) {
                $this->addErrorMessage($e->getMessage());
                return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
            }

            $this->addSuccessMessage(sprintf('Payment #%d updated.', $idPaymentMethod));

            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'idPaymentMethod' => $idPaymentMethod,
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

        $idPaymentMethod = $this->castId($request->request->get(PaymentMethodsTable::PARAM_ID_PAYMENT_METHOD));

        if (empty($idPaymentMethod)) {
            $this->addErrorMessage('Missing Payment Method id!');

            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        }

        try {
            $this
                ->getFactory()
                ->getMerchantFacade()
                ->removePaymentMethod($idPaymentMethod);
        } catch (PropelException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        } catch (PaymentMethodNotFoundException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        } catch (AmbiguousComparisonException $e) {
            $this->addErrorMessage($e->getMessage());
            return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
        }

        $this->addSuccessMessage(sprintf('Payment Method with id "%d" successfully deleted.', $idPaymentMethod));

        return $this->redirectResponse(self::PAYMENT_METHOD_LISTING_URL);
    }
}
