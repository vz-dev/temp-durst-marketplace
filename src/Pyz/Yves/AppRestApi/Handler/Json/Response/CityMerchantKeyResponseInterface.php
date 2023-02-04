<?php
/**
 * Durst - project - CityMerchantKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-18
 * Time: 09:54
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface CityMerchantKeyResponseInterface
{
    public const KEY_CITY = 'city';

    public const KEY_MERCHANTS = 'merchants';
    public const KEY_MERCHANTS_ID = 'id';
    public const KEY_MERCHANTS_LOGO = 'logo_url';
    public const KEY_MERCHANTS_NAME = 'name';
    public const KEY_MERCHANTS_STREET = 'street';
    public const KEY_MERCHANTS_ZIP = 'zip';
    public const KEY_MERCHANTS_CITY = 'city';
    public const KEY_MERCHANTS_PHONE = 'phone';
    public const KEY_MERCHANTS_TERMS = 'terms_of_service';
    public const KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY = 'heidelpay_public_key';
    public const KEY_MERCHANTS_PAYMENT_METHODS = 'payment_methods';
    public const KEY_MERCHANTS_B2C_PAYMENT_METHODS = 'b2c_payment_methods';
    public const KEY_MERCHANTS_B2B_PAYMENT_METHODS = 'b2b_payment_methods';
    public const KEY_MERCHANT_IS_WHOLESALE = 'is_wholesale';
    public const KEY_MERCHANTS_COMMENTS_ENABLED = 'comments_enabled';
    public const KEY_MERCHANTS_BRANCH_CODE = 'branch_code';
    public const KEY_MERCHANTS_OFFERS_DEPOSIT_PICKUP = 'offers_deposit_pickup';
    public const KEY_MERCHANTS_GRAPHMASTER_SETTINGS = 'tour_v2_settings';

    public const KEY_MERCHANTS_BILLING = 'billing';
    public const KEY_MERCHANTS_BILLING_COMPANY = 'company';
    public const KEY_MERCHANTS_BILLING_STREET = 'street';
    public const KEY_MERCHANTS_BILLING_STREET_NUMBER = 'street_number';
    public const KEY_MERCHANTS_BILLING_ZIP_CODE = 'zip_code';
    public const KEY_MERCHANTS_BILLING_CITY = 'city';
    public const KEY_MERCHANTS_BILLING_SALES_TAX_ID = 'sales_tax_id';
    public const KEY_MERCHANTS_BILLING_PLACE_JURISDICTION = 'place_jurisdiction';
    public const KEY_MERCHANTS_BILLING_ECO_CONTROL_NUMBER = 'eco_control_number';
    public const KEY_MERCHANTS_BILLING_PERSON_RESPONSIBLE = 'person_responsible';

    public const KEY_PAYMENT_PROVIDER = 'payment_provider';
    public const KEY_PAYMENT_PROVIDER_NAME = 'name';
    public const KEY_PAYMENT_PROVIDER_SEPA_MANDATE_URL = 'sepa_mandate_url';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHODS = 'payment_methods';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_KEY = 'key';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_NAME = 'name';
    public const KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_IMG_URL = 'img_url';
}
