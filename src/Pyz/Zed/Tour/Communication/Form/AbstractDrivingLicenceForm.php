<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.08.18
 * Time: 13:00
 */

namespace Pyz\Zed\Tour\Communication\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AbstractDrivingLicenceForm extends AbstractType
{
    public const FIELD_NAME = 'name';
    public const FIELD_DESCRIPTION = 'description';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addNameField($builder)
            ->addDescriptionField($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AbstractDrivingLicenceForm
     */
    protected function addNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AbstractDrivingLicenceForm
     */
    protected function addDescriptionField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(self::FIELD_DESCRIPTION, TextType::class, [
                'label' => 'Beschreibung',
                'required' => false,
            ]);

        return $this;
    }

}
