<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 08.08.18
 * Time: 16:54
 */

namespace Pyz\Zed\Tour\Communication\Form;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DrivingLicenceEditForm extends AbstractDrivingLicenceForm
{

    public const FIELD_CODE = 'code';
    public const BUTTON_SUBMIT = 'submit';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addCodeField($builder)
            ->addSubmitButton($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return DrivingLicenceEditForm
     */
    protected function addCodeField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_CODE, TextType::class, [
                'label' => 'Code',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => array(
                    'readonly' => true,
                ),
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return DrivingLicenceEditForm
     */
    protected function addSubmitButton(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::BUTTON_SUBMIT, SubmitType::class, [
                'label' => 'Aktualisieren',
            ]);

        return $this;
    }
}
