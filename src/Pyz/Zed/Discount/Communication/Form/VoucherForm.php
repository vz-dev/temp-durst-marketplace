<?php
/**
 * Durst - project - VoucherForm.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.03.21
 * Time: 18:17
 */

namespace Pyz\Zed\Discount\Communication\Form;

use Pyz\Zed\Discount\Communication\Form\Constraint\AllowedCharsAndLengthConstraint;
use Pyz\Zed\Discount\Communication\Form\Constraint\GeneratedCharLengthConstraint;
use Spryker\Zed\Discount\Communication\Form\VoucherForm as SprykerVoucherForm;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class VoucherForm extends SprykerVoucherForm
{
    /**
     * @param FormBuilderInterface $builder
     * @return $this|VoucherForm
     */
    protected function addCustomCodeField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_CUSTOM_CODE,
            TextType::class,
            [
                'required' => false,
                'constraints' => [
                    new AllowedCharsAndLengthConstraint()
                ]
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Spryker\Zed\Discount\Communication\Form\VoucherForm
     */
    protected function addRandomGeneratedCodeLength(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_RANDOM_GENERATED_CODE_LENGTH,
            ChoiceType::class,
            [
                'label' => 'Add Random Generated Code Length',
                'placeholder' => 'No additional random characters',
                'required' => false,
                'choices' => array_flip($this->createCodeLengthRangeList()),
                'choices_as_values' => true,
                'constraints' => [
                    new GeneratedCharLengthConstraint(),
                ]
            ]
        );

        return $this;
    }
}
