<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 11:39
 */

namespace Pyz\Zed\ProductManagement\Communication\Form;

use Spryker\Zed\ProductManagement\Communication\Form\ProductConcreteFormEdit as SprykerProductConcreteFormEdit;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductConcreteFormEdit extends SprykerProductConcreteFormEdit
{
    const FIELD_DEPOSIT = 'fk_deposit';

    const OPTION_DEPOSIT = 'OPTION_DEPOSIT';

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(self::OPTION_DEPOSIT);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $this
            ->addDepositField($builder, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return $this
     */
    protected function addDepositField(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_DEPOSIT, ChoiceType::class, [
                'label' => 'Pfand',
                'required' => true,
                'choices' => $options[self::OPTION_DEPOSIT],
            ]);

        return $this;
    }
}