<?php
/**
 * Durst - project - SoftwarPackageForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 21:15
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Form;


use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SoftwarePackageForm extends AbstractType
{
    public const FIELD_CODE = 'code';
    public const FIELD_NAME = 'name';
    public const FIELD_QUOTA_BRANCH = 'quotaBranch';
    public const FIELD_QUOTA_DELIVERY_AREA = 'quotaDeliveryArea';
    public const FIELD_QUOTA_ORDER = 'quotaOrder';
    public const FIELD_QUOTA_PRODUCT_CONCRETE = 'quotaProductConcrete';
    public const FIELD_PAYMENT_METHODS = 'paymentMethodIds';
    public const FIELD_SOFTWARE_FEATURES = 'softwareFeatureIds';
    public const FIELD_ALLOW_ORDER_COMMENTS = 'allowOrderComments';

    public const OPTION_PAYMENT_METHOD_CHOICES = 'OPTION_PAYMENT_METHOD_CHOICES';
    public const OPTION_SOFTWARE_FEATURE_CHOICES = 'OPTION_SOFTWARE_FEATURE_CHOICES';

    public const BUTTON_SUBMIT = 'submit';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => SoftwarePackageTransfer::class,
            ]);

        $resolver
            ->setRequired([
                static::OPTION_PAYMENT_METHOD_CHOICES,
                static::OPTION_SOFTWARE_FEATURE_CHOICES,
            ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        $this
            ->addCodeField($builder)
            ->addNameField($builder)
            ->addBranchQuotaField($builder)
            ->addDeliveryAreaQuotaField($builder)
            ->addOrderQuotaField($builder)
            ->addProductConcreteQuotaField($builder)
            ->addPaymentMethodField($builder, $options)
            ->addSoftwareFeatureField($builder, $options)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCodeField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_CODE, TextType::class, [
                'label' => 'Code',
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
    protected function addNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_NAME, TextType::class, [
                'label' => 'Name',
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
    protected function addBranchQuotaField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_QUOTA_BRANCH, IntegerType::class, [
                'label' => 'Filiallimit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDeliveryAreaQuotaField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_QUOTA_DELIVERY_AREA, IntegerType::class, [
                'label' => 'Liefergebietslimit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductConcreteQuotaField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_QUOTA_PRODUCT_CONCRETE, IntegerType::class, [
                'label' => 'Produktlimit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addOrderQuotaField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_QUOTA_ORDER, IntegerType::class, [
                'label' => 'Bestellungslimit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addPaymentMethodField(FormBuilderInterface $builder, array $options) : self
    {
        $builder
            ->add(self::FIELD_PAYMENT_METHODS, ChoiceType::class, array(
                'label' => 'Zahlungsmethoden',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices'  => $options[self::OPTION_PAYMENT_METHOD_CHOICES],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addSoftwareFeatureField(FormBuilderInterface $builder, array $options) : self
    {
        $builder
            ->add(self::FIELD_SOFTWARE_FEATURES, ChoiceType::class, array(
                'label' => 'Software-Features',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices'  => $options[self::OPTION_SOFTWARE_FEATURE_CHOICES],
            ));

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::BUTTON_SUBMIT, SubmitType::class, [
                'label' => 'Speichern',
            ]);

        return $this;
    }
}