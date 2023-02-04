<?php
/**
 * Durst - project - CategoryForm.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 17.06.21
 * Time: 09:14
 */

namespace Pyz\Zed\GraphMasters\Communication\Form;


use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryForm extends AbstractType
{
    public const OPTION_BRANCHES = 'OPTION_BRANCHES';
    public const OPTION_SLOT_SIZE = 'OPTION_SLOT_SIZE';
    public const OPTION_DELIVERY_AREA = 'OPTION_DELIVERY_AREA';

    public const BTN_SAVE = 'btnSave';

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([static::OPTION_BRANCHES, static::OPTION_SLOT_SIZE, static::OPTION_DELIVERY_AREA]);
        //$resolver->setDefault('data_class', GraphMastersDeliveryAreaCategoryTransfer::class);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $this
            ->addBranchField($builder, $options)
            ->addNameField($builder)
            ->addSlotSizeField($builder, $options)
            ->addEdtmCutoffSmallField($builder)
            ->addEdtmCutoffMediumField($builder)
            ->addEdtmCutoffLargeField($builder)
            ->addEdtmCutoffXlargeField($builder)
            ->addMinValueField($builder)
            ->addDeliveryAreaField($builder, $options)
            ->addIsActiveField($builder)
            ->addSaveButton($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addBranchField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::FK_BRANCH, ChoiceType::class, [
                'label' => 'Branch',
                'required' => true,
                'choices' => $options[static::OPTION_BRANCHES],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::CATEGORY_NAME, TextType::class, [
                'label' => 'Category Name',
                'required' => true
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addSlotSizeField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::SLOT_SIZE, ChoiceType::class, [
                'label' => 'Zeitfenstergröße in Stunden',
                'required' => false,
                'choices' => $options[static::OPTION_SLOT_SIZE],
                'choice_label' => function ($choice) {
                    return $choice;
                },
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addEdtmCutoffSmallField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::EDTM_CUTOFF_SMALL, IntegerType::class, [
                'label' => 'EDTM Cutoff Small',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addEdtmCutoffMediumField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::EDTM_CUTOFF_MEDIUM, IntegerType::class, [
                'label' => 'EDTM Cutoff Medium',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addEdtmCutoffLargeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::EDTM_CUTOFF_LARGE, IntegerType::class, [
                'label' => 'EDTM Cutoff Large',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addEdtmCutoffXlargeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::EDTM_CUTOFF_XLARGE, IntegerType::class, [
                'label' => 'EDTM Cutoff Xlarge',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addMinValueField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::MIN_VALUE, TextType::class, [
                'label' => 'MBW',
                'required' => true
            ]);

        return $this;
    }

    protected function addDeliveryAreaField(FormBuilderInterface $builder, array $options): self
    {
        $builder
            ->add(
                'deliveryAreaIds',
                Select2ComboBoxType::class,
                [
                    'label' => 'PLZs',
                    'placeholder' => false,
                    'multiple' => true,
                    'choices' => $options[static::OPTION_DELIVERY_AREA],
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIsActiveField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersDeliveryAreaCategoryTransfer::IS_ACTIVE, CheckboxType::class, [
                'label' => 'Aktiv?',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSaveButton(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::BTN_SAVE, SubmitType::class, [
                'label' => 'Speichern',
            ]);

        return $this;
    }
}
