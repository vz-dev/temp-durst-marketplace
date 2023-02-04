<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.01.18
 * Time: 16:52
 */

namespace Pyz\Yves\Checkout\Form\Steps;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchForm extends AbstractType
{
    const FORM_NAME = 'branchForm';

    const FIELD_BRANCH_SELECTION = 'fkBranch';

    const OPTION_BRANCHES = 'OPTION_BRANCHES';

    /**
     * @return string
     */
    public function getName()
    {
        return self::FORM_NAME;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(self::OPTION_BRANCHES);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addBranchSelectionField($builder, $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return $this
     */
    protected function addBranchSelectionField(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_BRANCH_SELECTION, ChoiceType::class, array(
                'label' => 'Branches',
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'choices'  => $options[self::OPTION_BRANCHES],
            ));

        return $this;
    }

}