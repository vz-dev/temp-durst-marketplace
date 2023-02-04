<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-03
 * Time: 11:35
 */

namespace Pyz\Zed\Discount\Communication\Form\Type;


use Spryker\Zed\Money\Communication\Form\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class DiscountMoneyType extends MoneyType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validationGroups = $options[static::OPTION_VALIDATION_GROUPS];

        $this
            ->addNetAmount($builder, static::FIELD_NET_AMOUNT, $validationGroups)
            ->addFieldAmount($builder, static::FIELD_GROSS_AMOUNT, $validationGroups)
            ->addFieldFkCurrency($builder)
            ->addFieldFkStore($builder);

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($validationGroups) {
                    $moneyCurrencyOptions = $this
                        ->getFactory()
                        ->createMoneyDataProvider()
                        ->getMoneyCurrencyOptionsFor($event->getData());

                    $this->configureHiddenMoneyInputs(
                        $event->getForm(),
                        static::FIELD_NET_AMOUNT,
                        $validationGroups,
                        $moneyCurrencyOptions
                    );

                    $this->configureMoneyInputs(
                        $event->getForm(),
                        static::FIELD_GROSS_AMOUNT,
                        $validationGroups,
                        $moneyCurrencyOptions
                    );
                }
            );
    }

    /**
     * @param FormInterface $form
     * @param string $fieldName
     * @param string $validationGroups
     * @param array $moneyCurrencyOptions
     * @return void
     */
    protected function configureHiddenMoneyInputs(
        FormInterface $form,
        string $fieldName,
        $validationGroups,
        array $moneyCurrencyOptions
    )
    {
        $field = $form
            ->get($fieldName);

        $options = $field
            ->getConfig()
            ->getOptions();

        $form
            ->remove($fieldName);

        $this
            ->addNetAmount(
                $form,
                $fieldName,
                $validationGroups,
                array_merge($options, $moneyCurrencyOptions)
            );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface|\Symfony\Component\Form\FormInterface $builder
     * @param string $fieldName
     * @param string $validationGroups
     * @param array $options
     * @return DiscountMoneyType
     */
    protected function addNetAmount($builder, string $fieldName, $validationGroups, array $options = []): self
    {
        $builder
            ->add(
                $fieldName,
                HiddenType::class
            );

        return $this;
    }
}