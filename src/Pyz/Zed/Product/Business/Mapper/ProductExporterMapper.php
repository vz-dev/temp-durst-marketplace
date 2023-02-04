<?php
/**
 * Durst - project - ProductExporterMapper.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 20:36
 */

namespace Pyz\Zed\Product\Business\Mapper;

/**
 * Class ProductExporterMapper
 * @package Pyz\Zed\Product\Business\Mapper
 */
class ProductExporterMapper
{

    public const PAYLOAD_KEY_SKU = 'sku';
    public const PAYLOAD_KEY_FK_DEPOSIT = 'fk_deposit';
    public const PAYLOAD_KEY_FK_PRODUCT_ABSTRACT = 'fk_product_abstract';
    public const PAYLOAD_KEY_ID_PRODUCT = 'id_product';
    public const PAYLOAD_KEY_ATTRIBUTES = 'attributes';
    public const PAYLOAD_KEY_ATTRIBUTES_NAME = 'name';
    public const PAYLOAD_KEY_ATTRIBUTES_UNIT = 'unit';


    public const EXPORT_KEY_SKU = 'durst_sku';
    public const EXPORT_KEY_PRODUCT_NAME = 'product_name';
    public const EXPORT_KEY_PRODUCT_UNIT = 'product_unit';
    public const EXPORT_KEY_MERCHANT_SKU = 'merchant_sku';
    public const EXPORT_KEY_MERCHANT_PRICE_NET = 'price_netto';
    public const EXPORT_KEY_MERCHANT_PRICE_GROSS = 'price_brutto';
    public const EXPORT_KEY_MERCHANT_PRODUCT_ACTIVE = 'product_active';

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param array $payload
     * @return array
     */
    public function map($payload) : array
    {
        $this->attributes = json_decode($payload[static::PAYLOAD_KEY_ATTRIBUTES], true);

        return [
            static::EXPORT_KEY_SKU => $payload[static::PAYLOAD_KEY_SKU],
            static::EXPORT_KEY_PRODUCT_NAME => $this->attributes[static::PAYLOAD_KEY_ATTRIBUTES_NAME],
            static::EXPORT_KEY_PRODUCT_UNIT => $this->attributes[static::PAYLOAD_KEY_ATTRIBUTES_UNIT],
            static::EXPORT_KEY_MERCHANT_SKU => '',
            static::EXPORT_KEY_MERCHANT_PRICE_NET => '',
            static::EXPORT_KEY_MERCHANT_PRICE_GROSS => '',
            static::EXPORT_KEY_MERCHANT_PRODUCT_ACTIVE => 0
        ];
    }

    /**
     * @return string
     */
    protected function getProductName() : string
    {
        return sprintf('%s %s',
            $this->attributes[static::PAYLOAD_KEY_ATTRIBUTES_NAME],
            $this->attributes[static::PAYLOAD_KEY_ATTRIBUTES_UNIT]
        );
    }


}