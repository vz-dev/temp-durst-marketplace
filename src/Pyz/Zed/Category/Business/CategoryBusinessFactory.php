<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Category\Business;

use Pyz\Zed\Category\Business\Map\CategoryDataPageMapBuilder;
use Pyz\Zed\Category\Business\Map\CategoryNodeDataPageMapBuilder;
use Pyz\Zed\Category\Business\Model\CategoryList\CategoryList;
use Pyz\Zed\Category\Business\Model\CategoryUrl\CategoryUrl;
use Spryker\Zed\Category\Business\CategoryBusinessFactory as SprykerCategoryBusinessFactory;

/**
 * @method \Pyz\Zed\Category\Persistence\CategoryQueryContainerInterface getQueryContainer()
 * @method \Pyz\Zed\Category\CategoryConfig getConfig()
 */
class CategoryBusinessFactory extends SprykerCategoryBusinessFactory
{
    /**
     * @return \Spryker\Zed\Category\Business\Model\CategoryUrl\CategoryUrlInterface
     */
    protected function createCategoryUrl()
    {
        $queryContainer = $this->getQueryContainer();
        $urlFacade = $this->getUrlFacade();
        $urlPathGenerator = $this->createUrlPathGenerator();

        return new CategoryUrl($queryContainer, $urlFacade, $urlPathGenerator);
    }

    /**
     * @return \Pyz\Zed\Category\Business\Map\CategoryNodeDataPageMapBuilder
     */
    public function createCategoryNodeDataPageMapBuilder()
    {
        return new CategoryNodeDataPageMapBuilder();
    }

    /**
     * @return CategoryList
     */
    public function createCategoryList() : CategoryList
    {
        return new CategoryList(
            $this->getQueryContainer()
        );
    }

    /**
     * @return CategoryDataPageMapBuilder
     */
    public function createCategoryDataPageMapBuilder() : CategoryDataPageMapBuilder
    {
        return new CategoryDataPageMapBuilder();
    }
}
