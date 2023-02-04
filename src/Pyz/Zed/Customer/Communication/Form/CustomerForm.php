<?php
/**
 * Durst - project - CustomerForm.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.11.21
 * Time: 10:40
 */

namespace Pyz\Zed\Customer\Communication\Form;

use Spryker\Zed\Customer\Communication\Form\CustomerForm as SprykerCustomerForm;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class CustomerForm extends SprykerCustomerForm
{
    public const FIELD_SECONDARY_MAIL = 'secondary_mail';
    public const FIELD_HEIDELPAY_REST_CUSTOMER_ID = 'heidelpay_rest_customer_id';
    public const FIELD_ACCEPT_NEWSLETTER = 'accept_newsletter';
    public const FIELD_IS_B2B = 'is_b2b';
    public const FIELD_COMPANY_REGISTRATION_NUMBER = 'company_registration_number';
    public const FIELD_PAYMENT_B2B = 'payment_b2b';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        parent::buildForm($builder, $options);

        $this
            ->addSecondaryMailField($builder)
            ->addHeidelpayRestCustomerIdField($builder)
            ->addAcceptNewsletterField($builder)
            ->addIsB2bField($builder)
            ->addCompanyRegistrationNumberField($builder)
            ->addPaymentB2bField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addSecondaryMailField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_SECONDARY_MAIL,
                EmailType::class,
                [
                    'label' => 'Secondary Email',
                    'required' => false,
                    'constraints' => [
                        new Email()
                    ]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addHeidelpayRestCustomerIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_HEIDELPAY_REST_CUSTOMER_ID,
                TextType::class,
                [
                    'label' => 'Heidelpay Customer',
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addAcceptNewsletterField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_ACCEPT_NEWSLETTER,
                CheckboxType::class,
                [
                    'label' => 'Newsletter ?',
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addIsB2bField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_IS_B2B,
                CheckboxType::class,
                [
                    'label' => 'B2B customer ?',
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCompanyRegistrationNumberField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_COMPANY_REGISTRATION_NUMBER,
                TextType::class,
                [
                    'label' => 'Company Registration Number',
                    'required' => false
                ]
            );

        return $this;
    }

    protected function addPaymentB2bField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_PAYMENT_B2B,
                TextType::class,
                [
                    'label' => 'B2B payment information',
                    'required' => false
                ]
            );

        return $this;
    }
}
