<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 04.01.18
 * Time: 10:07
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentMethodForm extends AbstractType
{
    const FORM_NAME = 'paymentMethodForm';

    const FIELD_ID = 'id';
    const FIELD_NAME = 'name';
    const FIELD_CODE = 'code';

    /**
     * @return string
     */
    public function getName()
    {
        return self::FORM_NAME;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addIdField($builder)
            ->addNameField($builder)
            ->addCodeField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_ID, TextType::class, [
                'label' => 'ID',
                'attr' => array(
                    'readonly' => true,
                ),
            ]);

        return $this;
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
                'required' => true,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCodeField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_CODE, TextType::class, [
                'label' => 'Code',
                'required' => true,
            ]);

        return $this;
    }
}