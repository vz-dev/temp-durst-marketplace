<?php
/**
 * Durst - project - TaxRateForm.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 14:19
 */

namespace Pyz\Zed\Tax\Communication\Form;

use Spryker\Zed\Tax\Communication\Form\TaxRateForm as SprykerTaxRateForm;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class TaxRateForm extends SprykerTaxRateForm
{
    protected const FIELD_VALID_FROM = 'validFrom';
    protected const FIELD_VALID_TO = 'validTo';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        parent::buildForm(
            $builder,
            $options
        );
        $this
            ->addValidFromField($builder)
            ->addValidToField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Spryker\Zed\Tax\Communication\Form\TaxRateForm
     */
    protected function addValidFromField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_VALID_FROM,
            DateType::class,
            [
                'label' => 'Von',
                'required' => false,
                'input' => 'string',
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Spryker\Zed\Tax\Communication\Form\TaxRateForm
     */
    protected function addValidToField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_VALID_TO,
            DateType::class,
            [
                'label' => 'Bis',
                'required' => false,
                'input' => 'string',
            ]
        );

        return $this;
    }
}
