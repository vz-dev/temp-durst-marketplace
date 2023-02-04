<?php
/**
 * Durst - project - AbstractMerchantForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.08.18
 * Time: 11:53
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Generated\Shared\Transfer\MerchantTransfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

abstract class AbstractMerchantForm extends AbstractType
{
    public const OPTION_SALUTATION_CHOICES = 'salutation_choices';
    public const OPTION_SOFTWARE_PACKAGE_CHOICES = 'OPTION_SOFTWARE_PACKAGE_CHOICES';
    public const OPTION_ACL_GROUPS = 'OPTION_ACL_GROUPS';
    public const OPTION_MERCHANT_PIN_LENGTH = 4;
    public const GROUP_UNIQUE_MERCHANTNAME_CHECK = 'unique_email_check';

    public const FIELD_MERCHANTNAME = 'merchantname';
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_SALUTATION = 'salutation';
    public const FIELD_COMPANY = 'company';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_STATUS = 'status';
    public const FIELD_SOFTWARE_PACKAGE = 'fkSoftwarePackage';
    public const FIELD_MERCHANT_PIN = 'merchantpin';
    public const FIELD_BILLING_PERIOD_PER_BRANCH = 'billingPeriodPerBranch';
    public const FIELD_ACL_GROUP = 'fkAclGroup';
    public const FIELD_LICENSE_FIXED = 'licenseFixed';
    public const FIELD_LICENSE_VARIABLE = 'licenseVariable';
    public const FIELD_LICENSE_NOTE = 'licenseNote';
    public const FIELD_REALAX_DEBITOR = 'realaxDebitor';
    public const FIELD_MARKETING_FIXED = 'marketingFixed';

    public const LABEL_BILLING_PERIOD_PER_BRANCH = 'Abrechnungsperioden pro Händler';
    protected const LABEL_ACL_GROUP = 'Gruppe';
    protected const LABEL_LICENSE_FIXED = 'Lizenzkosten (fix) in €';
    protected const LABEL_LICENSE_VARIABLE = 'Lizenzkosten (variabel) in € pro Gebinde';
    protected const LABEL_LICENSE_NOTE = 'Lizenznotizen';
    protected const LABEL_REALAX_DEBITOR = 'Debitorennummer Realax';
    protected const LABEL_MARKETING_FIXED = 'Marketingpauschale (fix) in €';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addEmailField($builder)
            ->addSalutationField($builder, $options)
            ->addFirstNameField($builder)
            ->addLastNameField($builder)
            ->addCompanyField($builder)
            ->addSoftwarePackageField($builder, $options)
            ->addAclGroupField($builder, $options)
            ->addMerchantPinField($builder)
            ->addBillingPeriodPerBranchField($builder)
            ->addMarketingFixedField($builder)
            ->addLicenseFixedField($builder)
            ->addLicenseVariableField($builder)
            ->addLicenseNoteField($builder)
            ->addRealaxDebitorField($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            static::OPTION_SALUTATION_CHOICES,
            static::OPTION_SOFTWARE_PACKAGE_CHOICES,
            static::OPTION_ACL_GROUPS
        ]);

        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $defaultData = $form->getConfig()->getData();
                $submittedData = $form->getData();

                if (array_key_exists(static::FIELD_MERCHANTNAME, $defaultData) === false ||
                    $defaultData[static::FIELD_MERCHANTNAME] !== $submittedData[static::FIELD_MERCHANTNAME]
                ) {
                    return [Constraint::DEFAULT_GROUP, static::GROUP_UNIQUE_MERCHANTNAME_CHECK];
                }

                return [Constraint::DEFAULT_GROUP];
            },
            'csrf_token_id'   => 'merchant_form',
            'data_class' => MerchantTransfer::class,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_MERCHANTNAME, TextType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFirstNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_FIRST_NAME, TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addLastNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_LAST_NAME, TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCompanyField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_COMPANY, TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addSalutationField(FormBuilderInterface $builder, array $options) : self
    {
        $builder->add(static::FIELD_SALUTATION, ChoiceType::class, [
            'choices' => $options[static::OPTION_SALUTATION_CHOICES],
        ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addSoftwarePackageField(FormBuilderInterface $builder, array $options) : self
    {
        $builder->add(static::FIELD_SOFTWARE_PACKAGE, ChoiceType::class, [
            'label' => 'Software-Paket',
            'choices' => $options[static::OPTION_SOFTWARE_PACKAGE_CHOICES],
        ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return MerchantForm
     */
    protected function addMerchantPinField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_MERCHANT_PIN,
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            [
                                'min' => static::OPTION_MERCHANT_PIN_LENGTH,
                                'max' => static::OPTION_MERCHANT_PIN_LENGTH
                            ]
                        )
                    ],
                    'label' => 'Merchant PIN'
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AbstractMerchantForm
     */
    protected function addBillingPeriodPerBranchField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_BILLING_PERIOD_PER_BRANCH,
                CheckboxType::class,
                [
                    'label' => self::LABEL_BILLING_PERIOD_PER_BRANCH,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addAclGroupField(FormBuilderInterface $builder, array $options): self
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
    protected function addMarketingFixedField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_MARKETING_FIXED,
                MoneyType::class,
                [
                    'label' => static::LABEL_MARKETING_FIXED,
                    'divisor' => 100,
                    'constraints' => [],
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addLicenseFixedField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_LICENSE_FIXED,
                MoneyType::class,
                [
                    'label' => static::LABEL_LICENSE_FIXED,
                    'divisor' => 100,
                    'constraints' => [],
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addLicenseVariableField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_LICENSE_VARIABLE,
                MoneyType::class,
                [
                    'label' => static::LABEL_LICENSE_VARIABLE,
                    'divisor' => 100,
                    'constraints' => [],
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addLicenseNoteField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_LICENSE_NOTE,
                TextareaType::class,
                [
                    'label' => static::LABEL_LICENSE_NOTE,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addRealaxDebitorField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_REALAX_DEBITOR,
                TextType::class,
                [
                    'label' => static::LABEL_REALAX_DEBITOR,
                    'required' => false
                ]
            );

        return $this;
    }
}
