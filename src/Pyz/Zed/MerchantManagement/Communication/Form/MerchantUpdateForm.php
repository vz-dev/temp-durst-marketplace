<?php


namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MerchantUpdateForm extends AbstractMerchantForm
{

    public const OPTION_STATUS_CHOICES = 'status_choices';

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(self::OPTION_STATUS_CHOICES);
    }

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
            ->addStatusField($builder, $options)
            ->addPasswordField($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return MerchantUpdateForm
     */
    protected function addStatusField(FormBuilderInterface $builder, array $options) : self
    {
        $builder->add(self::FIELD_STATUS, ChoiceType::class, [
            'choices' => $options[self::OPTION_STATUS_CHOICES],
        ]);

        return $this;
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
                'invalid_message' => 'The password fields must match.',
                'first_options' => ['label' => 'Password', 'attr' => ['autocomplete' => 'off']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['autocomplete' => 'off']],
                'required' => false,
                'type' => PasswordType::class,
            ]);

        return $this;
    }
}
