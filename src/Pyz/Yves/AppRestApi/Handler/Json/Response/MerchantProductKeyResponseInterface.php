<?php

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface MerchantProductKeyResponseInterface
{
    public const KEY_PRODUCT = 'product';

    public const KEY_PRODUCT_NAME = 'name';
    public const KEY_PRODUCT_SKU = 'sku';
    public const KEY_PRODUCT_IMAGE_BOTTLE = 'image_bottle';
    public const KEY_PRODUCT_IMAGE_BOTTLE_THUMB = 'image_bottle_thumb';
    public const KEY_PRODUCT_LOGO = 'product_logo';
    public const KEY_PRODUCT_DESCRIPTION = 'description';
    public const KEY_PRODUCT_INGREDIENTS = 'ingredients';
    public const KEY_PRODUCT_NUTRITIONAL_VALUES = 'nutritional_values';
    public const KEY_PRODUCT_ALCOHOL_BY_VOLUME = 'alcohol_by_volume';
    public const KEY_PRODUCT_ALLERGENS = 'allergens';
    public const KEY_PRODUCT_TAGS = 'tags';
    public const KEY_PRODUCT_BIO_CONTROL_AUTHORITY = 'bio_control_authority';
    public const KEY_PRODUCT_IMAGE_LIST = 'image_list';
    public const KEY_PRODUCT_FAT = 'fat';
    public const KEY_PRODUCT_KILOJOULES = 'kilojoules';
    public const KEY_PRODUCT_HEREOF_SUGAR = 'hereof_sugar';
    public const KEY_PRODUCT_KILOCALORIES = 'kilocalories';
    public const KEY_PRODUCT_CARBOHYDRATES = 'carbohydrates';
    public const KEY_PRODUCT_HEREOF_SATURATED_FATTY_ACIDS = 'hereof_saturated_fatty_acids';
    public const KEY_PRODUCT_SALT = 'salt';
    public const KEY_PRODUCT_PROTEINS = 'proteins';

    public const KEY_PRODUCT_MANUFACTURER = 'manufacturer';
    public const KEY_PRODUCT_MANUFACTURER_NAME = 'name';
    public const KEY_PRODUCT_MANUFACTURER_ADDRESS_1 = 'address_1';
    public const KEY_PRODUCT_MANUFACTURER_ADDRESS_2 = 'address_2';

    public const KEY_PRODUCT_UNITS = 'units';
    public const KEY_PRODUCT_UNIT_CURRENCY = 'currency';
    public const KEY_PRODUCT_UNIT_DEPOSIT = 'deposit';
    public const KEY_PRODUCT_UNIT_NAME = 'name';
    public const KEY_PRODUCT_UNIT_MATERIAL = 'material';
    public const KEY_PRODUCT_UNIT_CODE = 'code';
    public const KEY_PRODUCT_UNIT_PRIORITY = 'priority';
    public const KEY_PRODUCT_UNIT_VOLUME = 'volume';
    public const KEY_PRODUCT_UNIT_BOTTLE_VOLUME = 'bottle_volume';
    public const KEY_PRODUCT_UNIT_BOTTLES = 'bottles';
    public const KEY_PRODUCT_UNIT_IMAGE_BOTTLE_PLACEHOLDER_URL = 'bottleshot_product_unit_placeholder_url';
    public const KEY_PRODUCT_UNIT_IMAGE_CASE = 'caseshot_product_unit';

    public const KEY_PRODUCT_UNIT_PRICES = 'price';
    public const KEY_PRODUCT_UNIT_PRICE_PRICE = 'price';
    public const KEY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL = 'price_original';
    public const KEY_PRODUCT_UNIT_PRICE_DISCOUNT = 'discount';
    public const KEY_PRODUCT_UNIT_PRICE_UNIT_PRICE = 'unit_price';
    public const KEY_PRODUCT_UNIT_PRICE_IS_EXPIRED_DISCOUNT = 'is_expired_discount';
    public const KEY_PRODUCT_UNIT_PRICE_IS_CAROUSEL = 'is_carousel';
    public const KEY_PRODUCT_UNIT_PRICE_CAROUSEL_PRIORITY = 'carousel_priority';
}
