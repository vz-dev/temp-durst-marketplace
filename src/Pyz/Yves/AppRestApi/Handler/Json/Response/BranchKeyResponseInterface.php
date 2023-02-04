<?php
/**
 * Durst - project - BranchKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 15:30
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface BranchKeyResponseInterface
{
    public const KEY_ZIP_CODE_MERCHANTS_FOUND = 'zip_code_merchants_found';

    public const KEY_PAYMENT_PROVIDER = 'payment_provider';
    public const KEY_PAYMENT_PROVIDER_NAME = 'name';
    public const KEY_PAYMENT_PROVIDER_SEPA_MANDATE_URL = 'sepa_mandate_url';

    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHODS = 'payment_methods';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_KEY = 'key';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_NAME = 'name';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_IMG_URL = 'img_url';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_SHOW_DEBIT_SCREEN = 'show_debit_screen';

    public const KEY_MERCHANTS = 'merchants';
    public const KEY_MERCHANTS_ID = 'id';
    public const KEY_MERCHANTS_LOGO = 'logo_url';
    public const KEY_MERCHANTS_NAME = 'name';
    public const KEY_MERCHANTS_STREET = 'street';
    public const KEY_MERCHANTS_ZIP = 'zip';
    public const KEY_MERCHANTS_CITY = 'city';
    public const KEY_MERCHANTS_PHONE = 'phone';
    public const KEY_MERCHANTS_PAYMENT_METHODS = 'payment_methods';
    public const KEY_MERCHANTS_B2C_PAYMENT_METHODS = 'b2c_payment_methods';
    public const KEY_MERCHANTS_B2B_PAYMENT_METHODS = 'b2b_payment_methods';
    public const KEY_MERCHANTS_TERMS = 'terms_of_service';
    public const KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY = 'heidelpay_public_key';
    public const KEY_MERCHANTS_COMMENTS_ENABLED = 'comments_enabled';
    public const KEY_MERCHANT_IS_WHOLESALE = 'is_wholesale';

    public const KEY_CATEGORIES = 'categories';
    public const KEY_CATEGORY_ID = 'id';
    public const KEY_CATEGORY_NAME = 'name';

    public const KEY_CATEGORY_PRODUCTS = 'products';
    public const KEY_CATEGORY_PRODUCT_SKU = 'sku';
    public const KEY_CATEGORY_PRODUCT_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE_THUMB = 'image_bottle_thumb';
    public const KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE = 'image_bottle';
    public const KEY_CATEGORY_PRODUCT_IMAGE_LIST = 'image_list';
    public const KEY_CATEGORY_PRODUCT_LOGO = 'product_logo';
    public const KEY_CATEGORY_PRODUCT_ALCOHOL_BY_VOLUME = 'alcohol_by_volume';
    public const KEY_CATEGORY_PRODUCT_ALLERGENS = 'allergens';
    public const KEY_CATEGORY_PRODUCT_TAGS = 'tags';
    public const KEY_CATEGORY_PRODUCT_BIO_CONTROL_AUTHORITY = 'bio_control_authority';

    public const KEY_CATEGORY_PRODUCT_UNITS = 'units';
    public const KEY_CATEGORY_PRODUCT_UNIT_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRIORITY = 'priority';
    public const KEY_CATEGORY_PRODUCT_UNIT_SKU = 'sku';
    public const KEY_CATEGORY_PRODUCT_UNIT_CURRENCY = 'currency';
    public const KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT = 'deposit';
    public const KEY_CATEGORY_PRODUCT_UNIT_DISCOUNT = 'discount';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_THUMB = 'bottleshot_product_unit_thumb';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE = 'bottleshot_product_unit';
    public const KEY_CATEGORY_PRODUCT_UNIT_IMAGE_CASE = 'caseshot_product_unit';

    public const KEY_CATEGORY_PRODUCT_UNIT_PRICES = 'prices';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_MERCHANT_ID = 'merchant_id';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE = 'price';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL = 'price_original';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_DISCOUNT = 'discount';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE = 'unit_price';
    public const KEY_CATEGORY_PRODUCT_UNIT_PRICE_OUT_OF_STOCK = 'out_of_stock';

    public const KEY_CATEGORY_PRODUCT_UNIT_ATTRIBUTES = 'attributes';
    public const KEY_CATEGORY_PRODUCT_UNIT_ATTRIBUTE_VOLUME = 'volume';

    public const KEY_CATEGORY_PRODUCT_ATTRIBUTES = 'attributes';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_INGREDIENTS = 'ingredients';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_NUTRITIONAL_VALUES = 'nutritional_values';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_DESCRIPTION = 'description';

    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER = 'manufacturer';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_NAME = 'name';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_IMAGE = 'image';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_ADDRESS_1 = 'address_1';
    public const KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_ADDRESS_2 = 'address_2';
}
