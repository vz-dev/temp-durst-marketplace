<?php


namespace Pyz\Yves\Checkout\Form\Steps;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConcreteTimeSlotForm extends AbstractType
{
    const FORM_NAME = 'concreteTimeSlotForm';
    const OPTION_CONCRETE_TIME_SLOTS = 'OPTION_CONCRETE_TIME_SLOTS';

    const FIELD_START_TIME = 'startTime';
    const FIELD_END_TIME = 'endTime';

    const BUTTON_SAVE = 'save';

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addStartTimeField($builder)
            ->addEndTimeField($builder)
            ->addSaveButton($builder);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return self::FORM_NAME;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addStartTimeField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_START_TIME, TimeType::class, array(
                'label' => 'Start',
                'input' => 'string',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ));

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addEndTimeField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_END_TIME, TimeType::class, array(
                'label' => 'Ende',
                'input' => 'string',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ));

        return $this;
    }

    protected function addSaveButton(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::BUTTON_SAVE, SubmitType::class, array(
                'label' => 'Weiter',
            ));

        return $this;
    }
}
