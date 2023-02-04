<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 14:21
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TermsOfServiceForm extends AbstractType
{
    const FORM_NAME = 'termsOfServiceForm';

    const FIELD_TEXT = 'fieldText';
    const FIELD_HINT_TEXT = 'fieldHintText';
    const FIELD_BUTTON_TEXT = 'fieldButtonText';
    const FIELD_ID = 'fieldId';
    const FIELD_ACTIVE_UNTIL = 'fieldActiveUntil';
    const FIELD_NAME = 'fieldName';

    /**
     * @return string
     */
    public function getName()
    {
        return self::FORM_NAME;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addIdField($builder)
            ->addNameField($builder)
            ->addHintTextField($builder)
            ->addButtonTextField($builder)
            ->addTextField($builder)
            ->addActiveUntilField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_ID, HiddenType::class, [
                'label' => 'ID',
                'attr' => [
                    'readonly' => true,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addTextField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_TEXT, TextareaType::class, [
                'label' => 'Text',
                'required' => false,
                'attr' => [
                    'class' => 'html-editor',
                    'rows' => 10,
                ]
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => 'Name',
                'required' => true,
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
    protected function addHintTextField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_HINT_TEXT, TextType::class, [
                'label' => 'Hinweis',
                'required' => true,
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
    protected function addButtonTextField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_BUTTON_TEXT, TextType::class, [
                'label' => 'Button Text',
                'required' => true,
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
    protected function addActiveUntilField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_ACTIVE_UNTIL, DateTimeType::class, [
                'input' => 'string',
                'widget' => 'choice',
                'required' => false,
                'format' => \DateTime::ATOM,
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                    'hour' => 'Hour',
                    'minute' => 'Minute',
                    'second' => 'Second',
                ],
            ]);

        return $this;
    }


}