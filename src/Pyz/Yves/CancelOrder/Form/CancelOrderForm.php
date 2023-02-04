<?php
/**
 * Durst - project - CancelOrderForm.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.09.21
 * Time: 12:53
 */

namespace Pyz\Yves\CancelOrder\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelOrderForm extends AbstractType
{
    public const FIELD_MAIL = 'mail';
    public const FIELD_TOKEN = 'token';

    public const OPTION_TOKEN = 'option_token';

    protected const LABEL_MAIL = 'Adresse bei Bestellung';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(
        OptionsResolver $resolver
    ): void
    {
        parent::configureOptions($resolver);

        // not working, maybe because of session problem
        // between www.<APPLICATION_ENV> and customer.<APPLICATION_ENV>
        $resolver
            ->setDefaults(
                [
                    'csrf_protection' => false,
//                    'csrf_protection' => true,
//                    'csrf_field_name' => 'csrf',
//                    'csrf_token_id' => 'cancel_order'
                ]
            );

        $resolver
            ->setRequired(
                static::OPTION_TOKEN
            );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void
    {
        $this
            ->addMailField($builder)
            ->addTokenField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return $this
     */
    protected function addMailField(
        FormBuilderInterface $builder
    ): self
    {
        $builder
            ->add(
                static::FIELD_MAIL,
                EmailType::class,
                [
                    'label' => static::LABEL_MAIL
                ]
            );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addTokenField(
        FormBuilderInterface $builder,
        array $options
    ): self
    {
        $builder
            ->add(
                static::FIELD_TOKEN,
                HiddenType::class,
                [
                    'attr' => [
                        'value' => $options[static::OPTION_TOKEN]
                    ]
                ]
            );

        return $this;
    }
}
