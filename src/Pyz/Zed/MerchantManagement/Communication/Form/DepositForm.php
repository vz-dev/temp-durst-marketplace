<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 25.10.17
 * Time: 13:14
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DepositForm extends AbstractType
{
    const FIELD_NAME = 'name';
    const FIELD_PRICE = 'price';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addNameField($builder)
            ->addPriceField($builder, $options);
    }


    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addPriceField(FormBuilderInterface $builder, array $options)
    {

        $fieldOptions = [
            'label' => 'Price *',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ];


        $builder->add(static::FIELD_PRICE, MoneyType::class, $fieldOptions);

        return $this;
    }

}