<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-08
 * Time: 12:32
 */

namespace Pyz\Zed\Discount\Communication\Form;

use Spryker\Zed\Discount\Communication\Form\GeneralForm as SprykerGeneralForm;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GeneralForm extends SprykerGeneralForm
{
    public const FIELD_DISCOUNT_NAME = 'discount_name';
    public const FIELD_DISCOUNT_SKU = 'discount_sku';
    public const FIELD_BRANCH = 'fk_branch';

    public const OPTION_BRANCH_LIST = 'options_branch';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addDiscountName($builder)
            ->addDiscountSku($builder)
            ->addBranch($builder, $options);

        parent::buildForm($builder, $options);
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
     * @return GeneralForm
     */
    protected function addDisplayNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_DISPLAY_NAME,
                TextType::class,
                [
                    'label' => 'Name (A unique name that will be generated)',
                    'required' => false,
                    'disabled' => true
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return GeneralForm
     */
    protected function addDiscountName(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_DISCOUNT_NAME,
                TextType::class,
                [
                    'label' => 'Name of discount (Will be used for billing)',
                    'required' => true,
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return GeneralForm
     */
    protected function addDiscountSku(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_DISCOUNT_SKU,
                TextType::class,
                [
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return GeneralForm
     */
    protected function addBranch(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(
                static::FIELD_BRANCH,
                ChoiceType::class,
                [
                    'label' => 'Branch',
                    'required' => false,
                    'choices' => $options[static::OPTION_BRANCH_LIST]
                ]
            );

        return $this;
    }
}