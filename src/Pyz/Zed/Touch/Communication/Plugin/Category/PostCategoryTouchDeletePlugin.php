<?php
/**
 * Durst - project - PostCategoryRemovePluginitial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.11.18
 * Time: 09:50
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Category;

use Pyz\Shared\Category\CategoryConstants;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Zed\Category\Dependency\Plugin\CategoryRelationDeletePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostCategoryTouchDeletePlugin
 * @package Pyz\Zed\Touch\Communication\Plugin
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 * @method \Pyz\Zed\Touch\Persistence\TouchQueryContainer getQueryContainer()
 */
class PostCategoryTouchDeletePlugin extends AbstractPlugin implements CategoryRelationDeletePluginInterface
{
    /**
     * @param int $idCategory
     *
     * @return void
     */
    public function delete($idCategory)
    {
        $this->getFacade()->touchDeleted(CategoryConstants::RESOURCE_TYPE_CATEGORY, $idCategory);
        $categoryProducts = $this->getQueryContainer()->queryProductsByCategoryId($idCategory)->find();

        $this->removeCategoryProducts($categoryProducts->getData());
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct[] $categoryProducts
     *
     * @return void
     */
    protected function removeCategoryProducts(array $categoryProducts)
    {
        $productIds = [];
        foreach ($categoryProducts as $categoryProduct) {
            $productIds[] = $categoryProduct->getIdProduct();
        }

        $this->getFacade()->bulkTouchSetDeleted(ProductConstants::RESOURCE_TYPE_PRODUCT, $productIds);
    }
}
