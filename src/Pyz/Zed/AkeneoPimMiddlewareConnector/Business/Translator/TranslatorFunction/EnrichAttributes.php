<?php
/**
 * Durst - project - EnrichAttributes.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.19
 * Time: 11:01
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Translator\TranslatorFunction;

use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\Translator\TranslatorFunction\EnrichAttributes as SrykerEcoEnrichAttributes;

class EnrichAttributes extends SrykerEcoEnrichAttributes
{
    /**
     * @param mixed $value
     * @param array $payload
     *
     * @return mixed
     */
    public function translate($value, array $payload)
    {
        $this->initAttributeOptionMap();

        foreach ($value as $attributeKey => $attributeValues) {
            if ($this->isKeySkipped($attributeKey)) {
                continue;
            }

            if (!$this->hasKey($attributeKey) || $this->isKeyExcluded($attributeKey)) {
                continue;
            }

            $isAttributeLocalizable = $this->isAttributeLocalizable($attributeKey);

            foreach ($attributeValues as $index => $attributeValue) {
                $attributeData = $attributeValue[static::KEY_DATA];
                $locale = $attributeValue[static::KEY_LOCALE];

                if ($attributeData === null) {
                    unset($value[$attributeKey]);
                    continue 2;
                }

                if ($isAttributeLocalizable) {
                    $options = is_array($attributeData) ? $this->getArrayOptions($attributeKey, $attributeData) : $this->getOptions($attributeKey, $attributeData);
                    if (!array_key_exists($locale, $options)) {
                        unset($value[$attributeKey]);
                        continue;
                    }

                    $value[$attributeKey][$index][static::KEY_DATA] = $options[$locale];
                    continue;
                }

                $value[$attributeKey] = $this->getAttributeValue($attributeKey, $attributeData);
            }
        }

        return $value;
    }

    /**
     * @return void
     */
    protected function initAttributeOptionMap(): void
    {
        if (static::$attributeOptionMap !== null) {
            return;
        }

        $map = [];
        foreach ($this->getMap() as $key => $value){
            $map[$value['attribute_key']] = $value;
        }

        static::$attributeOptionMap = array_map(
            function ($element) {
                return $element[static::KEY_OPTIONS];
            },
            array_filter($map, function ($element) {
                return count($element[static::KEY_OPTIONS] ?? []) > 0 &&
                    in_array($element[static::KEY_TYPE], static::ATTRIBUTE_TYPES_WITH_OPTIONS);
            })
        );

        static::$attributeLocalizableMap = array_map(
            function ($element) {
                return $element[static::KEY_LOCALIZABLE];
            },
            array_filter($map, function ($element) {
                return in_array($element[static::KEY_TYPE], static::ATTRIBUTE_TYPES_WITH_OPTIONS);
            })
        );

        static::$attributesForSkippingMap = array_map(
            function ($element) {
                return $element[static::KEY_KEY];
            },
            array_filter($map, function ($element) {
                return in_array($element[static::KEY_TYPE], static::ATTRIBUTES_TYPES_FOR_SKIPPING) && $element[static::KEY_KEY] != static::ATTRIBUTE_PRICE;
            })
        );
    }
}