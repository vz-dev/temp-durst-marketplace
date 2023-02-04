<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 12.11.18
 * Time: 09:40
 */

namespace Pyz\Shared\Category;

use Spryker\Shared\Category\CategoryConstants as SprykerCategoryConstants;

interface CategoryConstants extends SprykerCategoryConstants
{

    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a category
     */
    public const RESOURCE_TYPE_CATEGORY = 'RESOURCE_TYPE_CATEGORY';



    /**
     * Name of category type inside Elasticsearch
     */
    public const CATEGORY_SEARCH_TYPE = 'product_category';
}