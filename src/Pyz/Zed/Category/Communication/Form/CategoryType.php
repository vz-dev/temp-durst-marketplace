<?php
/**
 * Durst - project - CategoryType.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 10:24
 */

namespace Pyz\Zed\Category\Communication\Form;

use Spryker\Zed\Category\Communication\Form\CategoryType as SprykerCategoryType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends SprykerCategoryType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addLocalizedAttributesForm(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_LOCALIZED_ATTRIBUTES, CollectionType::class, [
            'entry_type' => CategoryLocalizedAttributeType::class,
        ]);

        return $this;
    }
}