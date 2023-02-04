<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-08
 * Time: 13:21
 */

namespace Pyz\Zed\Discount\Communication\Form;

use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\DiscountGeneralTransfer;
use Spryker\Zed\Discount\Communication\Form\DiscountForm as SprykerDiscountForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountForm extends SprykerDiscountForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addGeneralSubFormWithOptions($builder, $options)
            ->addCalculatorSubForm($builder)
            ->addConditionsSubForm($builder);

        $this
            ->executeFormTypeExpanderPlugins($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                GeneralForm::OPTION_BRANCH_LIST => []
            ]);

        parent::configureOptions($resolver);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return DiscountForm
     */
    protected function addGeneralSubFormWithOptions(FormBuilderInterface $builder, array $options): self
    {
        $defaultOptions = [
            'data_class' => DiscountGeneralTransfer::class,
            'label' => false,
            GeneralForm::OPTION_BRANCH_LIST => $options[GeneralForm::OPTION_BRANCH_LIST]
        ];

        $builder
            ->add(
                DiscountConfiguratorTransfer::DISCOUNT_GENERAL,
                GeneralForm::class,
                $defaultOptions
            );

        return $this;
    }
}