<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-08
 * Time: 12:26
 */

namespace Pyz\Zed\Discount\Communication\Controller;

use DateTime;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Spryker\Zed\Discount\Communication\Controller\IndexController as SprykerIndexController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexController
 * @package Pyz\Zed\Discount\Communication\Controller
 * @method DiscountFacadeInterface getFacade()
 * @method \Pyz\Zed\Discount\Communication\DiscountCommunicationFactory getFactory()
 */
class IndexController extends SprykerIndexController
{
    protected const VALID_TO_END_HOUR = 23;
    protected const VALID_TO_END_MINUTES = 59;
    protected const VALID_TO_END_SECONDS = 59;

    /**
     * @param Request $request
     * @return array|RedirectResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAction(Request $request)
    {
        $discountForm = $this
            ->getFactory()
            ->createDiscountForm();

        $discountForm
            ->handleRequest($request);

        if ($discountForm->isSubmitted() && $discountForm->isValid()) {
            $formData = $this
                ->getFormData($discountForm);

            $idDiscount = $this
                ->getFacade()
                ->saveDiscount($formData);

            $discountType = $formData
                ->getDiscountGeneral()
                ->getDiscountType();

            $this->addSuccessMessage('Discount successfully created, but not activated.');

            return new RedirectResponse(
                $this
                ->createEditRedirectUrl($idDiscount, $discountType)
            );
        }

        $discountFormTabs = $this
            ->getFactory()
            ->createDiscountFormTabs($discountForm);

        return $this
            ->viewResponse([
                'discountForm' => $discountForm->createView(),
                'discountFormTabs' => $discountFormTabs->createView()
            ]);
    }

    /**
     * @return array
     */
    public function generateAction(): array
    {
        $cartDiscountGroups = $this
            ->getFacade()
            ->generateCartDiscountGroups();

        return $this
            ->viewResponse(
                [
                    'discounts' => $cartDiscountGroups
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Form\FormInterface $discountForm
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function handleDiscountForm(Request $request, FormInterface $discountForm): void
    {
        $discountForm->handleRequest($request);

        if ($discountForm->isSubmitted()) {
            if ($discountForm->isValid()) {
                /* @var $formData DiscountConfiguratorTransfer */
                $formData = $discountForm
                    ->getData();

                $moneyCollection = $formData
                    ->getDiscountCalculator()
                    ->getMoneyValueCollection();

                foreach ($moneyCollection as $money) {
                    $grossAmount = round($money->getGrossAmount());

                    $tax = $this
                        ->getFactory()
                        ->getTaxFacade()
                        ->getDefaultTaxRateForDate(
                            new DateTime('now')
                        );
                    $taxRate = 1 + ($tax / 100);

                    $netAmount = round($money->getGrossAmount() / $taxRate);

                    $money
                        ->setNetAmount($netAmount)
                        ->setGrossAmount($grossAmount);
                }

                $validTo = $formData
                    ->getDiscountGeneral()
                    ->getValidTo();

                $formData
                    ->getDiscountGeneral()
                    ->setValidTo(
                        $this
                            ->getValidToWithEndDate(
                                $validTo
                            )
                    );

                $isUpdated = $this
                    ->getFacade()
                    ->updateDiscount($formData);

                if ($isUpdated === true) {
                    $this->addSuccessMessage('Discount successfully updated.');
                }
            } else {
                $this->addErrorMessage('Please fill all required fields.');
            }
        }
    }

    /**
     * @param FormInterface $form
     * @return DiscountConfiguratorTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormData(FormInterface $form): DiscountConfiguratorTransfer
    {
        /* @var $formData DiscountConfiguratorTransfer */
        $formData = $form
            ->getData();

        $discountGeneral = $formData
            ->getDiscountGeneral();

        $displayNameGenerator = $this
            ->getFacade()
            ->getDiscountDisplayNameGenerator();

        $idBranch = $discountGeneral
            ->getFkBranch();

        if ($idBranch === null) {
            $idBranch = 0;
        }

        $displayName = $displayNameGenerator
            ->generateDisplayName($idBranch);

        $discountGeneral
            ->setDisplayName($displayName);

        $moneyCollection = $formData
            ->getDiscountCalculator()
            ->getMoneyValueCollection();

        foreach ($moneyCollection as $money) {
            $grossAmount = round($money->getGrossAmount());

            $tax = $this
                ->getFactory()
                ->getTaxFacade()
                ->getDefaultTaxRateForDate(
                    new DateTime('now')
                );
            $taxRate = 1 + ($tax / 100);

            $netAmount = round($money->getGrossAmount() / $taxRate);

            $money
                ->setNetAmount($netAmount)
                ->setGrossAmount($grossAmount);
        }

        $validTo = $discountGeneral
            ->getValidTo();

        $discountGeneral
            ->setValidTo(
                $this
                    ->getValidToWithEndDate(
                        $validTo
                    )
            );

        return $formData;
    }

    /**
     * @param \DateTime $validTo
     * @return \DateTime
     */
    protected function getValidToWithEndDate(DateTime $validTo): DateTime
    {
        $validTo
            ->setTime(
                static::VALID_TO_END_HOUR,
                static::VALID_TO_END_MINUTES,
                static::VALID_TO_END_SECONDS
            );

        return $validTo;
    }
}
