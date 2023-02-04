<?php
/**
 * Durst - project - BranchForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.04.18
 * Time: 16:42
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchUpdateForm extends AbstractBranchForm
{
    protected const LABEL_BUTTON_SUBMIT = 'Speichern';

    public const FIELD_DISPATCHER_EMAIL = 'dispatcherEmail';
    public const FIELD_DISPATCHER_PHONE = 'dispatcherPhone';
    public const FIELD_DISPATCHER_NAME = 'dispatcherName';

    public const FIELD_ACCOUNTING_EMAIL = 'accountingEmail';
    public const FIELD_ACCOUNTING_PHONE = 'accountingPhone';
    public const FIELD_ACCOUNTING_NAME = 'accountingName';

    public const FIELD_MARKETING_EMAIL = 'marketingEmail';
    public const FIELD_MARKETING_PHONE = 'marketingPhone';
    public const FIELD_MARKETING_NAME = 'marketingName';

    public const FIELD_SERVICE_EMAIL = 'serviceEmail';
    public const FIELD_SERVICE_PHONE = 'servicePhone';
    public const FIELD_SERVICE_NAME = 'serviceName';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->addB2CPaymentMethodField($builder, $options)
            ->addB2BPaymentMethodField($builder, $options)
            ->addExportAccountField($builder)
            ->addExportContraAccountField($builder)
            ->addDispatcherNameField($builder)
            ->addDispatcherEmailField($builder)
            ->addDispatcherPhoneField($builder)
            ->addAccountingEmailField($builder)
            ->addAccountingNameField($builder)
            ->addAccountingPhoneField($builder)
            ->addMarketingEmailField($builder)
            ->addMarketingNameField($builder)
            ->addMarketingPhoneField($builder)
            ->addServiceEmailField($builder)
            ->addServiceNameField($builder)
            ->addServicePhoneField($builder)
            ->addExportCsvEnabledField($builder);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            self::OPTION_MERCHANT_OPTIONS,
            self::OPTION_SALUTATION_OPTIONS,
            self::OPTION_PAYMENT_METHOD_CHOICES,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addDispatcherNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_DISPATCHER_NAME, TextType::class, array(
                'label' => 'Name',
                'required' => true,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addDispatcherEmailField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_DISPATCHER_EMAIL, EmailType::class, array(
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new Email(),
                ],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addDispatcherPhoneField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_DISPATCHER_PHONE, TextType::class, array(
                'label' => 'Telefon',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addAccountingNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_ACCOUNTING_NAME, TextType::class, array(
                'label' => 'Name',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addAccountingEmailField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_ACCOUNTING_EMAIL, EmailType::class, array(
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new Email(),
                ],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addAccountingPhoneField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_ACCOUNTING_PHONE, TextType::class, array(
                'label' => 'Telefon',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addMarketingNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_MARKETING_NAME, TextType::class, array(
                'label' => 'Name',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addMarketingEmailField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_MARKETING_EMAIL, EmailType::class, array(
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new Email(),
                ],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addMarketingPhoneField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_MARKETING_PHONE, TextType::class, array(
                'label' => 'Telefon',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addServiceNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_SERVICE_NAME, TextType::class, array(
                'label' => 'Name',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addServiceEmailField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_SERVICE_EMAIL, EmailType::class, array(
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new Email(),
                ],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addServicePhoneField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_SERVICE_PHONE, TextType::class, array(
                'label' => 'Telefon',
                'required' => false,
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $additionalOptions
     * @return $this
     */
    protected function addCodeField(FormBuilderInterface $builder, array $additionalOptions = []): AbstractBranchForm
    {
        $options = array_merge([
            'attr' => [
                'readonly' => true
            ]
        ], $additionalOptions);

        parent::addCodeField($builder, $options);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCodeField2(FormBuilderInterface $builder): AbstractBranchForm
    {
        $builder
            ->add(self::FIELD_CODE, TextType::class, [
                'label' => 'Branch-Code',
                'required' => false,
                'attr' => [
                    'readonly' => true
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addB2CPaymentMethodField(FormBuilderInterface $builder, array $options) : self
    {
        $builder
            ->add(self::FIELD_PAYMENT_METHOD_B2C, ChoiceType::class, [
                'label' => self::LABEL_PAYMENT_METHODS_B2C,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $options[self::OPTION_PAYMENT_METHOD_CHOICES],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return BranchUpdateForm
     */
    protected function addB2BPaymentMethodField(FormBuilderInterface $builder, array $options) : self
    {
        $builder
            ->add(self::FIELD_PAYMENT_METHOD_B2B, ChoiceType::class, [
                'label' => self::LABEL_PAYMENT_METHODS_B2B,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $options[self::OPTION_PAYMENT_METHOD_CHOICES],
                'choice_attr' => function ($key, $val, $index) {
                    if (in_array($val, self::DISABLED_PAYMENT_METHODS_FOR_B2B)) {
                        return ['disabled' => 'disabled'];
                    }
                    return [];
                },
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addExportAccountField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_EXPORT_ACCOUNT,
                TextType::class,
                [
                    'label' => static::LABEL_EXPORT_ACCOUNT,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addExportContraAccountField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_EXPORT_CONTRA_ACCOUNT,
                TextType::class,
                [
                    'label' => static::LABEL_EXPORT_CONTRA_ACCOUNT,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addExportCsvEnabledField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_EXPORT_CSV_ENABLED,
                CheckboxType::class,
                [
                    'label' => static::LABEL_EXPORT_CSV_ENABLED,
                    'required' => false
                ]
            );

        return $this;
    }
}
