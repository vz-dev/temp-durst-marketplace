<?php
/**
 * Durst - project - CustomerUpdateForm.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.11.21
 * Time: 10:58
 */

namespace Pyz\Zed\Customer\Communication\Form;


use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerUpdateForm extends CustomerForm
{
    public const FIELD_DEFAULT_BILLING_ADDRESS = 'default_billing_address';
    public const FIELD_DEFAULT_SHIPPING_ADDRESS = 'default_shipping_address';

    public const OPTION_ADDRESS_CHOICES = 'address_choices';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(self::OPTION_ADDRESS_CHOICES);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addDefaultBillingAddressField($builder, $options[self::OPTION_ADDRESS_CHOICES])
            ->addDefaultShippingAddressField($builder, $options[self::OPTION_ADDRESS_CHOICES]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Spryker\Zed\Customer\Communication\Form\CustomerUpdateForm
     */
    protected function addEmailField(FormBuilderInterface $builder)
    {
        $builder->add(self::FIELD_EMAIL, EmailType::class, [
            'label' => 'Email',
            'constraints' => $this->createEmailConstraints(),
            'disabled' => 'disabled',
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addDefaultBillingAddressField(FormBuilderInterface $builder, array $choices)
    {
        $builder->add(self::FIELD_DEFAULT_BILLING_ADDRESS, ChoiceType::class, [
            'label' => 'Billing Address',
            'placeholder' => 'Select one',
            'choices' => array_flip($choices),
            'choices_as_values' => true,
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addDefaultShippingAddressField(FormBuilderInterface $builder, array $choices)
    {
        $builder->add(self::FIELD_DEFAULT_SHIPPING_ADDRESS, ChoiceType::class, [
            'label' => 'Shipping Address',
            'placeholder' => 'Select one',
            'choices' => array_flip($choices),
            'choices_as_values' => true,
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'customer';
    }

    /**
     * @deprecated Use `getBlockPrefix()` instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
