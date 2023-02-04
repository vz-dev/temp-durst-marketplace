<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.01.18
 * Time: 14:07
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SalutationForm extends AbstractType
{
    const FORM_NAME = 'salutationForm';

    const FIELD_ID = 'id';
    const FIELD_NAME = 'name';

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
            ->addNameField($builder);
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
}