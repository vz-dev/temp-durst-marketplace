<?php
/**
 * Durst - project - MerchantProductsKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 12:24
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface MerchantProductsKeyResponseInterface
{
    public const KEY_CATEGORIES = 'categories';
    public const KEY_CATEGORY_ID = 'id';
    public const KEY_CATEGORY_NAME = 'name';
    public const KEY_CATEGORY_IMAGE_URL = 'image_url';
    public const KEY_CATEGORY_COLOR_CODE = 'color_code';
    public const KEY_CATEGORY_PRIORITY = 'priority';

    public const KEY_SUBCATEGORIES = 'sub_categories';

    public const KEY_CATEGORY_PRODUCTS = 'products';
    public const KEY_CATEGORY_PRODUCT_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_SKU = 'sku';
    public const KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE = 'image_bottle';
    public const KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE_THUMB = 'image_bottle_thumb';
    public const KEY_CATEGORY_PRODUCT_LOGO = 'product_logo';
    public const KEY_CATEGORY_PRODUCT_DESCRIPTION = 'description';
    public const KEY_CATEGORY_PRODUCT_INGREDIENTS = 'ingredients';
    public const KEY_CATEGORY_PRODUCT_NUTRITIONAL_VALUES = 'nutritional_values';
    public const KEY_CATEGORY_PRODUCT_ALCOHOL_BY_VOLUME = 'alcohol_by_volume';
    public const KEY_CATEGORY_PRODUCT_ALLERGENS = 'allergens';
    public const KEY_CATEGORY_PRODUCT_TAGS = 'tags';
    public const KEY_CATEGORY_PRODUCT_BIO_CONTROL_AUTHORITY = 'bio_control_authority';
    public const KEY_CATEGORY_PRODUCT_IMAGE_LIST = 'image_list';
    public const KEY_CATEGORY_PRODUCT_RELEVANCE = 'relevance';

    public const KEY_CATEGORY_PRODUCT_MANUFACTURER = 'manufacturer';
    public const KEY_CATEGORY_PRODUCT_MANUFACTURER_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_MANUFACTURER_ADDRESS_1 = 'address_1';
    public const KEY_CATEGORY_PRODUCT_MANUFACTURER_ADDRESS_2 = 'address_2';

    public const KEY_CATEGORY_PRODUCT_UNITS = 'units';
    public const KEY_CATEGORY_PRODUCT_UNIT_CURRENCY = 'currency';
    public const KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT = 'deposit';
    public const KEY_CATEGORY_PRODUCT_UNIT_WEIGHT = 'weight';
    public const KEY_CATEGORY_PRODUCT_UNIT_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_UNIT_MATERIAL = 'material';
    public const KEY_CATEGORY_PRODUCT_UNIT_SKU = 'sku';
    public const KEY_CATEGORY_PRODUCT_UNIT_CODE = 'code';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRIORITY = 'priority';
    public const KEY_CATEGORY_PRODUCT_UNIT_VOLUME = 'volume';
    public const KEY_CATEGORY_PRODUCT_UNIT_BOTTLE_VOLUME = 'bottle_volume';
    public const KEY_CATEGORY_PRODUCT_UNIT_BOTTLES = 'bottles';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_THUMB = 'bottleshot_product_unit_thumb';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE = 'bottleshot_product_unit';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_PLACEHOLDER_URL = 'bottleshot_product_unit_placeholder_url';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_CASE = 'caseshot_product_unit';
    public const KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT_TYPE = 'deposit_type';

    public const KEY_CATEGORY_PRODUCT_UNIT_PRICES = 'price';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE = 'price';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL = 'price_original';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_DISCOUNT = 'discount';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE = 'unit_price';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_IS_EXPIRED_DISCOUNT = 'is_expired_discount';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_IS_CAROUSEL = 'is_carousel';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_CAROUSEL_PRIORITY = 'carousel_priority';
}
