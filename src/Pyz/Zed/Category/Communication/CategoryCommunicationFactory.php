<?php
/**
 * Durst - project - CategoryCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 10:18
 */

namespace Pyz\Zed\Category\Communication;

use Pyz\Zed\Category\Communication\Form\CategoryType;
use Spryker\Zed\Category\Communication\CategoryCommunicationFactory as SprykerCategoryCommunicationFactory;

class CategoryCommunicationFactory extends SprykerCategoryCommunicationFactory
{
    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCategoryEditForm()
    {
        $categoryCreateDataFormProvider = $this->createCategoryEditFormDataProvider();
        $formFactory = $this->getFormFactory();

        return $formFactory->create(
            CategoryType::class,
            $categoryCreateDataFormProvider->getData(),
            $categoryCreateDataFormProvider->getOptions()
        );
    }
}