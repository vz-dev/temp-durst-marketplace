<?php

namespace Pyz\Zed\GraphMasters\Communication\Form;

use Generated\Shared\Transfer\GraphMastersOpeningTimeTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OpeningTimeForm extends AbstractType
{
    protected const FIELD_WEEKDAY = 'weekday';
    protected const FIELD_START_TIME = 'startTime';
    protected const FIELD_END_TIME = 'endTime';
    protected const FIELD_PAUSE_START_TIME = 'pauseStartTime';
    protected const FIELD_PAUSE_END_TIME = 'pauseEndTime';

    protected const LABEL_WEEKDAY = 'Wochentag';
    protected const LABEL_START_TIME = 'Startzeit';
    protected const LABEL_END_TIME = 'Endzeit';
    protected const LABEL_PAUSE_START_TIME = 'Pausen-Startzeit';
    protected const LABEL_PAUSE_END_TIME = 'Pausen-Endzeit';

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
        $resolver->setDefault('data_class', GraphMastersOpeningTimeTransfer::class);
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
            ->addEndTimeField($builder)
            ->addPauseStartTimeField($builder)
            ->addPauseEndTimeField($builder);
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

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPauseStartTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_PAUSE_START_TIME,
                TimeType::class,
                [
                    'label' => static::LABEL_PAUSE_START_TIME,
                    'input' => 'string',
                    'widget' => 'single_text',
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPauseEndTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_PAUSE_END_TIME,
                TimeType::class,
                [
                    'label' => static::LABEL_PAUSE_END_TIME,
                    'input' => 'string',
                    'widget' => 'single_text',
                ]
            );

        return $this;
    }
}
