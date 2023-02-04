<?php
/**
 * Durst - project - MerchantUserUpdateForm.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 13:20
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Generated\Shared\Transfer\MerchantUserTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MerchantUserUpdateForm extends AbstractType
{
    public const OPTION_SALUTATION_OPTIONS = 'OPTION_SALUTATION_OPTIONS';
    public const OPTION_MERCHANT_OPTIONS = 'OPTION_MERCHANT_OPTIONS';
    public const OPTION_ACL_GROUPS = 'OPTION_ACL_GROUPS';

    public const FIELD_MERCHANT = 'fkMerchant';
    public const FIELD_SALUTATION = 'salutation';
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_EMAIL = 'email';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_ACL_GROUP = 'fkAclGroup';

    protected const LABEL_MERCHANT = 'Händler';
    protected const LABEL_ACL_GROUP = 'Gruppe';

    public const BUTTON_SUBMIT = 'submit';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        $this
            ->addMerchantField($builder, $options)
            ->addSalutationField($builder, $options)
            ->addFirstNameField($builder)
            ->addLastNameField($builder)
            ->addEmailField($builder)
            ->addPasswordField($builder)
            ->addAclGroupField($builder, $options)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(
                [
                    static::OPTION_ACL_GROUPS,
                    static::OPTION_MERCHANT_OPTIONS,
                    static::OPTION_SALUTATION_OPTIONS
                ]
            );

        $resolver
            ->setDefaults(
                [
                    'data_class' => MerchantUserTransfer::class,
                    'csrf_token_id' => 'merchant_user_update_form'
                ]
            );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addMerchantField(
        FormBuilderInterface $builder,
        array $options
    ): self
    {
        $builder
            ->add(
                static::FIELD_MERCHANT,
                ChoiceType::class,
                [
                    'choices' => $options[static::OPTION_MERCHANT_OPTIONS],
                    'label' => static::LABEL_MERCHANT
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addSalutationField(
        FormBuilderInterface $builder,
        array $options
    ): self
    {
        $builder
            ->add(
                static::FIELD_SALUTATION,
                ChoiceType::class,
                [
                    'choices' => $options[static::OPTION_SALUTATION_OPTIONS]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addFirstNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_FIRST_NAME,
                TextType::class,
                [
                    'constraints' =>
                        [
                            new NotBlank()
                        ]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addLastNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_LAST_NAME,
                TextType::class,
                [
                    'constraints' =>
                        [
                            new NotBlank()
                        ]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_EMAIL,
                TextType::class,
                [
                    'constraints' =>
                        [
                            new NotBlank()
                        ]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addPasswordField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_PASSWORD,
                RepeatedType::class,
                [
                    'constraints' =>
                        [
                            new NotBlank()
                        ],
                    'invalid_message' => 'The password fields must match.',
                    'first_options' =>
                        [
                            'label' => 'Password',
                            'attr' =>
                                [
                                    'autocomplete' => 'off'
                                ]
                        ],
                    'second_options' =>
                        [
                            'label' => 'Repeat Password',
                            'attr' =>
                                [
                                    'autocomplete' => 'off'
                                ]
                        ],
                    'required' => true,
                    'type' => PasswordType::class
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addAclGroupField(
        FormBuilderInterface $builder,
        array $options
    ): self
    {
        $builder
            ->add(
                static::FIELD_ACL_GROUP,
                ChoiceType::class,
                [
                    'choices' => $options[static::OPTION_ACL_GROUPS],
                    'label' => static::LABEL_ACL_GROUP
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::BUTTON_SUBMIT,
                SubmitType::class,
                [
                    'label' => 'Speichern'
                ]
            );

        return $this;
    }
}
