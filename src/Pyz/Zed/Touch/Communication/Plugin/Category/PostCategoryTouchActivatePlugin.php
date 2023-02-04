<?php
/**
 * Durst - project - PostCategoryTouchActivatePlugin
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 11:19
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Category;

use Generated\Shared\Transfer\CategoryTransfer;
use Pyz\Shared\Category\CategoryConstants;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Zed\Category\Dependency\Plugin\CategoryRelationUpdatePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostCategoryTouchActivatePlugin
 * @package Pyz\Zed\Touch\Communication\Plugin
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 * @method \Pyz\Zed\Touch\Persistence\TouchQueryContainer getQueryContainer()
 */
class PostCategoryTouchActivatePlugin extends AbstractPlugin implements CategoryRelationUpdatePluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\CategoryTransfer $categoryTransfer
     *
     * @return void
     */
    public function update(CategoryTransfer $categoryTransfer)
    {
        $idCategory = $categoryTransfer->getIdCategory();

        $this->getFacade()->touchActive(CategoryConstants::RESOURCE_TYPE_CATEGORY, $idCategory);

        $categoryProducts = $this->getQueryContainer()->queryProductsByCategoryId($idCategory)->find();

        $this->addCategoryProducts($categoryProducts->getData());
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct[] $categoryProducts
     *
     * @return void
     */
    protected function addCategoryProducts(array $categoryProducts)
    {
        $productIds = [];

        foreach ($categoryProducts as $categoryProduct) {
            $productIds[] = $categoryProduct->getIdProduct();
        }

        $this->getFacade()->bulkTouchSetActive(ProductConstants::RESOURCE_TYPE_PRODUCT, $productIds);
    }
}
