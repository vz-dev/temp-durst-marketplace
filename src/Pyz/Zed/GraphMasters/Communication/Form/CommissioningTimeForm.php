<?php

namespace Pyz\Zed\GraphMasters\Communication\Form;

use Generated\Shared\Transfer\GraphMastersCommissioningTimeTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommissioningTimeForm extends AbstractType
{
    protected const FIELD_WEEKDAY = 'weekday';
    protected const FIELD_START_TIME = 'startTime';
    protected const FIELD_END_TIME = 'endTime';

    protected const LABEL_WEEKDAY = 'Wochentag';
    protected const LABEL_START_TIME = 'Startzeit';
    protected const LABEL_END_TIME = 'Endzeit';

    protected const CHOICES_WEEKDAY = [
        'Montag' => 'monday',
        'Dienstag' => 'tuesday',
        'Mittwoch' => 'wednesday',
        'Donnerstag' => 'thursday',
        'Freitag' => 'friday',
        'Samstag' => 'saturday',
    ];

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', GraphMastersCommissioningTimeTransfer::class);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addWeekdayField($builder)
            ->addStartTimeField($builder)
            ->addEndTimeField($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addWeekdayField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_WEEKDAY,
                ChoiceType::class,
                [
                    'label' => self::LABEL_WEEKDAY,
                    'label_attr' => ['class' => 'required'],
                    'choices' => self::CHOICES_WEEKDAY,
                    'attr' => ['required' => 'required'],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addStartTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_START_TIME,
                TimeType::class,
                [
                    'label' => static::LABEL_START_TIME,
                    'label_attr' => ['class' => 'required'],
                    'input' => 'string',
                    'widget' => 'single_text',
                    'attr' => ['required' => 'required'],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEndTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_END_TIME,
                TimeType::class,
                [
                    'label' => static::LABEL_END_TIME,
                    'label_attr' => ['class' => 'required'],
                    'input' => 'string',
                    'widget' => 'single_text',
                    'attr' => ['required' => 'required'],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

        return $this;
    }
}
