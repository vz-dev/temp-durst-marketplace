<?php
/**
 * Durst - project - CategoryLocalizedAttributeType.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 10:19
 */

namespace Pyz\Zed\Category\Communication\Form;

use Spryker\Zed\Category\Communication\Form\CategoryLocalizedAttributeType as SprykerCategoryLocalizedAttributeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryLocalizedAttributeType extends SprykerCategoryLocalizedAttributeType
{
    const FIELD_IMAGE_URL = 'imageUrl';
    const FIELD_COLOR_CODE = 'colorCode';
    const FIELD_PRIORITY = 'priority';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $this
            ->addImageUrlField($builder)
            ->addColorCodeField($builder)
            ->addPriorityField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageUrlField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_IMAGE_URL, TextType::class, [
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addColorCodeField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_COLOR_CODE, TextType::class, [
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPriorityField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_PRIORITY, NumberType::class, [
                'constraints' => [
                ],
                'required' => false,
            ]);

        return $this;
    }
}