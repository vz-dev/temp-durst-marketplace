<?php
/**
 * Durst - project - SoftwareFeatureForm.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 02.11.18
 * Time: 10:47
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Form;


use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SoftwareFeatureForm extends AbstractType
{
    public const FIELD_CODE = 'code';
    public const FIELD_NAME = 'name';
    public const FIELD_DESCRIPTION = 'description';

    public const LABEL_CODE = 'Code';
    public const LABEL_NAME = 'Name';
    public const LABEL_DESCRIPTION = 'Beschreibung';
    public const LABEL_SUBMIT = 'Speichern';


    public const BUTTON_SUBMIT = 'submit';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => SoftwareFeatureTransfer::class,
            ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    )
    {
        $this
            ->addCodeField($builder)
            ->addNameField($builder)
            ->addDescriptionField($builder)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCodeField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_CODE, TextType::class, [
                'label' => static::LABEL_CODE,
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
    protected function addNameField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_NAME, TextType::class, [
                'label' => static::LABEL_NAME,
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
    protected function addDescriptionField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::FIELD_DESCRIPTION, TextType::class, [
                'label' => static::LABEL_DESCRIPTION,
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
    protected function addSubmitButton(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(static::BUTTON_SUBMIT, SubmitType::class, [
                'label' => static::LABEL_SUBMIT,
            ]);

        return $this;
    }
}