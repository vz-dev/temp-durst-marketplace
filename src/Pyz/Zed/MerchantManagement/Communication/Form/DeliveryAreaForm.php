<?php


namespace Pyz\Zed\MerchantManagement\Communication\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class DeliveryAreaForm extends AbstractType
{
    const FIELD_NAME = 'name';
    const FIELD_CITY = 'city';
    const FIELD_ZIP = 'zip';

    /**
     * @return string
     */
    public function getName()
    {
        return 'deliveryArea';
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
            ->addNameField($builder)
            ->addCityField($builder)
            ->addZipField($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {

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
     *
     * @return $this
     */
    protected function addCityField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_CITY, TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addZipField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_ZIP, IntegerType::class, [
                'empty_data' => '50825',
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => '10000',
                        'max' => '99999'
                    ])
                ],
            ]);

        return $this;
    }

}
