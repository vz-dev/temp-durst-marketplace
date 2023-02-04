<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Catalog;

use Pyz\Yves\Catalog\ActiveSearchFilter\UrlGenerator;
use Spryker\Yves\Kernel\AbstractFactory;

class CatalogFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Yves\Catalog\ActiveSearchFilter\UrlGeneratorInterface
     */
    public function createActiveSearchFilterUrlGenerator()
    {
        return new UrlGenerator($this->getSearchClient());
    }

    /**
     * @return \Spryker\Client\Category\CategoryClientInterface
     */
    public function getCategoryClient()
    {
        return $this->getProvidedDependency(CatalogDependencyProvider::CLIENT_CATEGORY);
    }

    /**
     * @return \Spryker\Client\Locale\LocaleClientInterface
     */
    public function getLocaleClient()
    {
        return $this->getProvidedDependency(CatalogDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return \Spryker\Client\ProductCategoryFilter\ProductCategoryFilterClientInterface
     */
    public function getProductCategoryFilterClient()
    {
        return $this->getProvidedDependency(CatalogDependencyProvider::CLIENT_PRODUCT_CATEGORY_FILTER);
    }

    /**
     * @return \Spryker\Client\Search\SearchClientInterface
     */
    protected function getSearchClient()
    {
        return $this->getProvidedDependency(CatalogDependencyProvider::CLIENT_SEARCH);
    }
}
