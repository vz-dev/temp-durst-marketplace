<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.01.18
 * Time: 09:22
 */

namespace Pyz\Zed\TermsOfService\Communication\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermsOfServiceForm extends AbstractType
{
    const FORM_NAME = 'termsOfServiceForm';

    const BUTTON_ACCEPT = 'accept';

    const OPTION_BUTTON_LABEL = 'OPTION_BUTTON_LABEL';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addAcceptButton($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(self::OPTION_BUTTON_LABEL);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::FORM_NAME;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addAcceptButton(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::BUTTON_ACCEPT, SubmitType::class, [
                'label' => $options[self::OPTION_BUTTON_LABEL],
            ]);

        return $this;
    }
}