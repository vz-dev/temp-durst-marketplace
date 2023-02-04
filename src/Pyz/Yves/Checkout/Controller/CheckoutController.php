<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Checkout\Controller;

use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\Checkout\Form\Voucher\VoucherForm;
use Pyz\Yves\Checkout\Plugin\Provider\CheckoutControllerProvider;
use Spryker\Yves\StepEngine\Process\StepEngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Yves\Checkout\CheckoutFactory getFactory()
 */
class CheckoutController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $response = $this->createStepProcess()->process($request);

        return $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function customerAction(Request $request)
    {
        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createCustomerFormCollection()
        );

        return $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function addressAction(Request $request)
    {
        $response = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createAddressFormCollection()
        );

        return $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function shipmentAction(Request $request)
    {
        return $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createShipmentFormCollection()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function paymentAction(Request $request)
    {
        return $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createPaymentFormCollection()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function summaryAction(Request $request)
    {
        $viewData = $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createSummaryFormCollection()
        );

        return $viewData;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function placeOrderAction(Request $request)
    {
        return $this->createStepProcess()->process($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function successAction(Request $request)
    {
        return $this->createStepProcess()->process($request);
    }

    /**
     * @return array
     */
    public function errorAction()
    {
        return [];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function addVoucherAction(Request $request)
    {
        $form = $this->getFactory()
            ->createCheckoutFormFactory()
            ->getVoucherForm()
            ->handleRequest($request);

        if ($form->isValid()) {
            $voucherCode = $form->get(VoucherForm::FIELD_VOUCHER_DISCOUNTS)->getData();

            $this->getFactory()
                ->createVoucherHandler()
                ->add($voucherCode);
        }

        return $this->redirectResponseInternal(CheckoutControllerProvider::CHECKOUT_SUMMARY);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Process\StepEngine|StepEngineInterface
     */
    protected function createStepProcess()
    {
        return $this->getFactory()->createCheckoutProcess();
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function branchAction(Request $request)
    {
        return $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createBranchFormCollection()
        );
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function concreteTimeSlotAction(Request $request)
    {
        return $this->createStepProcess()->process(
            $request,
            $this->getFactory()
                ->createCheckoutFormFactory()
                ->createConcreteTimeSlotFormCollection()
        );
    }
}
