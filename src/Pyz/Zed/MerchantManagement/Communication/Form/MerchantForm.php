<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class MerchantForm
 * @package Pyz\Zed\MerchantManagement\Communication\Form
 */
class MerchantForm extends AbstractMerchantForm
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addPasswordField($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPasswordField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_PASSWORD, RepeatedType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'invalid_message' => 'The password fields must match.',
                'first_options' => ['label' => 'Password', 'attr' => ['autocomplete' => 'off']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['autocomplete' => 'off']],
                'required' => true,
                'type' => PasswordType::class,
            ]);

        return $this;
    }
}
