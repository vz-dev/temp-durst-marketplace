<?php
/**
 * Durst - project - CampaignAdvertisingMaterialType.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.06.21
 * Time: 09:20
 */

namespace Pyz\Zed\Campaign\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignAdvertisingMaterialType extends AbstractType
{
    protected const FIELD_ID_CAMPAIGN_ADVERTISING_MATERIAL = 'idCampaignAdvertisingMaterial';
    protected const FIELD_CAMPAIGN_ADVERTISING_MATERIAL_NAME = 'campaignAdvertisingMaterialName';
    protected const FIELD_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION = 'campaignAdvertisingMaterialDescription';
    protected const FIELD_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME = 'campaignAdvertisingMaterialLeadTime';
    protected const FIELD_IS_ACTIVE = 'isActive';
    protected const FIELD_SAVE = 'FIELD_SAVE';

    protected const LABEL_CAMPAIGN_ADVERTISING_MATERIAL_NAME = 'Name';
    protected const LABEL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION = 'Description';
    protected const LABEL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME = 'Lead time (weeks)';
    protected const LABEL_IS_ACTIVE = 'Active';
    protected const LABEL_SAVE = 'Save';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void
    {
        $this
            ->addIdField($builder)
            ->addCampaignAdvertisingMaterialNameField($builder)
            ->addCampaignAdvertisingMaterialDescriptionField($builder)
            ->addCampaignAdvertisingMaterialLeadTimeField($builder)
            ->addIsActiveField($builder)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addIdField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_ID_CAMPAIGN_ADVERTISING_MATERIAL,
                HiddenType::class
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignAdvertisingMaterialNameField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_ADVERTISING_MATERIAL_NAME,
                TextType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_ADVERTISING_MATERIAL_NAME
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignAdvertisingMaterialDescriptionField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION,
                TextareaType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_ADVERTISING_MATERIAL_DESCRIPTION,
                    'required' => false
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addCampaignAdvertisingMaterialLeadTimeField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                static::FIELD_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME,
                ChoiceType::class,
                [
                    'label' => static::LABEL_CAMPAIGN_ADVERTISING_MATERIAL_LEAD_TIME,
                    'choices' => range(
                        0,
                        52
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
