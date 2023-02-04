<?php
/**
 * Durst - project - SettingsForm.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 19:46
 */

namespace Pyz\Zed\GraphMasters\Communication\Form;


use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class SettingsForm extends AbstractType
{
    public const OPTION_BRANCHES = 'OPTION_BRANCHES';

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([static::OPTION_BRANCHES]);
        $resolver->setDefault('data_class', GraphMastersSettingsTransfer::class);
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
            ->addIsActiveField($builder)
            ->addDepotApiIdField($builder)
            ->addDepotPathField($builder)
            ->addOpeningTimeCollection($builder)
            ->addLeadTimeField($builder)
            ->addBufferTimeField($builder)
            ->addCommissioningTimeCollection($builder);
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
            ->add(GraphMastersSettingsTransfer::FK_BRANCH, ChoiceType::class, [
                'label' => 'Branch',
                'required' => true,
                'choices' => $options[static::OPTION_BRANCHES],
            ]);

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
            ->add(GraphMastersSettingsTransfer::IS_ACTIVE, CheckboxType::class, [
                'label' => 'Graphmasters für diesen Branch verwenden?',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDepotApiIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::DEPOT_API_ID, TextType::class, [
                'label' => 'Depot-API-ID',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDepotPathField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::DEPOT_PATH, TextType::class, [
                'label' => 'Depot Path',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addOpeningTimeCollection(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::OPENING_TIMES, CollectionType::class, [
                'label' => 'Öffnungzeiten',
                'entry_type' => OpeningTimeForm::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'constraints' => [
                    new Valid(),
                ],
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addLeadTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::LEAD_TIME, NumberType::class, [
                'label' => 'Vorlaufzeit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBufferTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::BUFFER_TIME, NumberType::class, [
                'label' => 'Pufferzeit',
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCommissioningTimeCollection(FormBuilderInterface $builder): self
    {
        $builder
            ->add(GraphMastersSettingsTransfer::COMMISSIONING_TIMES, CollectionType::class, [
                'label' => 'Kommissionierungszeiten',
                'entry_type' => CommissioningTimeForm::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'constraints' => [
                    new Valid(),
                ],
            ]);

        return $this;
    }
}
