<?php
/**
 * Durst - project - AddressForm.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-13
 * Time: 18:11
 */

namespace Pyz\Zed\Sales\Communication\Form;

use Spryker\Zed\Sales\Communication\Form\AddressForm as SprykerAddressForm;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressForm extends SprykerAddressForm
{
    public const FIELD_LAT = 'lat';
    public const FIELD_LNG = 'lng';
    public const FIELD_FLOOR = 'floor';
    public const FIELD_ELEVATOR = 'elevator';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addLatField($builder)
            ->addLngField($builder)
            ->addFloorField($builder)
            ->addElevatorField($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AddressForm
     */
    protected function addLatField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_LAT, NumberType::class, [
                'scale' => 7,
                'required' => true,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AddressForm
     */
    protected function addLngField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(static::FIELD_LNG, NumberType::class, [
                'scale' => 7,
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AddressForm
     */
    protected function addFloorField(FormBuilderInterface $builder) : self
    {
        $builder
            ->add(
                self::FIELD_FLOOR,
                IntegerType::class,
                ['required' => false]
            );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @return AddressForm
     */
    protected function addElevatorField(FormBuilderInterface $builder): self
    {
        $builder
            ->add(
                self::FIELD_ELEVATOR,
                CheckboxType::class,
                [
                    'required' => false
                ]
            );

        return $this;
    }

}
