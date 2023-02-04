<?php
/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Category;

use Pyz\Zed\Category\Communication\Plugin\PostCategoryTouchActivatePluginInterface;
use Pyz\Zed\Category\Communication\Plugin\PostCategoryTouchDeletePluginInterface;
use Pyz\Zed\Touch\Communication\Plugin\Category\PostCategoryTouchActivatePlugin;
use Pyz\Zed\Touch\Communication\Plugin\Category\PostCategoryTouchDeletePlugin;
use Spryker\Zed\Category\CategoryDependencyProvider as SprykerDependencyProvider;
use Spryker\Zed\CmsBlockCategoryConnector\Communication\Plugin\CategoryFormPlugin;
use Spryker\Zed\CmsBlockCategoryConnector\Communication\Plugin\ReadCmsBlockCategoryRelationsPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductCategory\Communication\Plugin\ReadProductCategoryRelationPlugin;
use Spryker\Zed\ProductCategory\Communication\Plugin\RemoveProductCategoryRelationPlugin;
use Spryker\Zed\ProductCategory\Communication\Plugin\UpdateProductCategoryRelationPlugin;

class CategoryDependencyProvider extends SprykerDependencyProvider
{
    /**
     * @return \Spryker\Zed\Category\Dependency\Plugin\CategoryRelationDeletePluginInterface[]
     */
    protected function getRelationDeletePluginStack()
    {
        $deletePlugins = array_merge(
            [
                new RemoveProductCategoryRelationPlugin(),
                new PostCategoryTouchDeletePlugin(),
            ],
            parent::getRelationDeletePluginStack()
        );

        return $deletePlugins;
    }

    /**
     * @return \Spryker\Zed\Category\Dependency\Plugin\CategoryRelationUpdatePluginInterface[]
     */
    protected function getRelationUpdatePluginStack()
    {
        return array_merge(
            [
                new UpdateProductCategoryRelationPlugin(),
                new CategoryFormPlugin(),
                new PostCategoryTouchActivatePlugin(),
            ],
            parent::getRelationUpdatePluginStack()
        );
    }

    /**
     * @return \Spryker\Zed\Category\Dependency\Plugin\CategoryRelationReadPluginInterface[]
     */
    protected function getRelationReadPluginStack()
    {
        $readPlugins = array_merge(
            [
                new ReadProductCategoryRelationPlugin(),
                new ReadCmsBlockCategoryRelationsPlugin(),
            ],
            parent::getRelationReadPluginStack()
        );

        return $readPlugins;
    }

    /**
     * @return array
     */
    protected function getCategoryFormPlugins()
    {
        return array_merge(parent::getCategoryFormPlugins(), [
            new CategoryFormPlugin(),
        ]);
    }
}
