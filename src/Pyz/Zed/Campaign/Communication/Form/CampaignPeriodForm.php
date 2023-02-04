<?php
/**
 * Durst - project - CampaignPeriodForm.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.06.21
 * Time: 15:42
 */

namespace Pyz\Zed\Campaign\Communication\Form;


use Spryker\Yves\Kernel\Form\AbstractType;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampaignPeriodForm extends AbstractType
{
    public const OPTION_ADVERTISING_MATERIAL_CHOICES = 'advertising_material_choices';

    protected const FIELD_ID_CAMPAIGN_PERIOD = 'idCampaignPeriod';
    protected const FIELD_CAMPAIGN_NAME = 'campaignName';
    protected const FIELD_CAMPAIGN_DESCRIPTION = 'campaignDescription';
    protected const FIELD_CAMPAIGN_START_DATE = 'campaignStartDate';
    protected const FIELD_CAMPAIGN_END_DATE = 'campaignEndDate';
    protected const FIELD_CAMPAIGN_LEAD_TIME = 'campaignLeadTime';
    protected const FIELD_IS_ACTIVE = 'isActive';
    protected const FIELD_ADVERTISING_MATERIAL = 'campaignAdvertisingMaterials';
    protected const FIELD_SAVE = 'FIELD_SAVE';

    protected const LABEL_CAMPAIGN_NAME = 'Name';
    protected const LABEL_CAMPAIGN_DESCRIPTION = 'Description';
    protected const LABEL_CAMPAIGN_START_DATE = 'Start Date';
    protected const LABEL_CAMPAIGN_END_DATE = 'End Date';
    protected const LABEL_CAMPAIGN_LEAD_TIME = 'Lead time (days)';
    protected const LABEL_IS_ACTIVE = 'Active';
    protected const LABEL_ADVERTISING_MATERIAL = 'Advertising material';
    protected const LABEL_SAVE = 'Save';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addIdField($builder)
            ->addCampaignNameField($builder)
            ->addCampaignDescriptionField($builder)
            ->addCampaignStartDateField($builder)
            ->addCampaignEndDateField($builder)
            ->addCampaignLeadTimeField($builder)
            ->addIsActiveField($builder)
            ->addAdvertisingMaterialsField($builder, $options)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(
                static::OPTION_ADVERTISING_MATERIAL_CHOICES
            );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_ID_CAMPAIGN_PERIOD,
                HiddenType::class
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_NAME,
                TextType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_NAME
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignDescriptionField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_DESCRIPTION,
                TextareaType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_DESCRIPTION,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignStartDateField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_START_DATE,
                TextType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_START_DATE
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignEndDateField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_END_DATE,
                TextType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_END_DATE
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignLeadTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_LEAD_TIME,
                ChoiceType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_LEAD_TIME,
                    'choices' => range(
                        0,
                        7
                    )
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addIsActiveField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_IS_ACTIVE,
                CheckboxType::class,
                [
                    'label' => static::LABEL_IS_ACTIVE,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addAdvertisingMaterialsField(
        FormBuilderInterface $builder,
        array $options
    ): self
    {
        $builder
            ->add(
                static::FIELD_ADVERTISING_MATERIAL,
                Select2ComboBoxType::class,
                [
                    'label' => static::LABEL_ADVERTISING_MATERIAL,
                    'placeholder' => false,
                    'multiple' => true,
                    'choices' => $options[static::OPTION_ADVERTISING_MATERIAL_CHOICES],
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_SAVE,
                SubmitType::class,
                [
                    'label' => static::LABEL_SAVE
                ]
            );

        return $this;
    }
}
