<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Shared\ProductSearch;

use Spryker\Shared\ProductSearch\ProductSearchConfig as SprykerProductSearchConstants;

interface ProductSearchConfig extends SprykerProductSearchConstants
{
    const PRODUCT_ABSTRACT_PAGE_SEARCH_TYPE = 'product_abstract';

    public const KEY_PRODUCT_SEARCH_BOTTLES = 'bottles';

    public const KEY_PRODUCT_SEARCH_VOLUME_PER_BOTTLE = 'volume_per_bottle';
}
