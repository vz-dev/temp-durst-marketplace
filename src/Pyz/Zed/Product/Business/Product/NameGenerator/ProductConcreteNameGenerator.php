<?php
/**
 * Durst - project - ProductConcreteNameGenerator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.07.20
 * Time: 09:25
 */

namespace Pyz\Zed\Product\Business\Product\NameGenerator;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\Product\Business\Product\NameGenerator\ProductConcreteNameGenerator as SprykerProductConcreteNameGenerator;

class ProductConcreteNameGenerator extends SprykerProductConcreteNameGenerator
{
    protected const KEY_NAME = 'name';
    protected const KEY_UNIT = 'unit';
    protected const DELIMITER = ' - ';

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    public function getLocalizedProductConcreteName(ProductConcreteTransfer $productConcreteTransfer, LocaleTransfer $localeTransfer)
    {
        foreach ($productConcreteTransfer->getLocalizedAttributes() as $localizedAttribute) {
            if ($localizedAttribute->getLocale()->getIdLocale() === $localeTransfer->getIdLocale()) {
                if($localizedAttribute->getName() !== null && $localizedAttribute->getName() !== ''){
                    return $localizedAttribute->getName();
                }
            }
        }

        $localizedProductConcreteName = $productConcreteTransfer->getSku();

        $attributes = $productConcreteTransfer->getAttributes();
        if(array_key_exists(self::KEY_NAME, $attributes) === true){
            $localizedProductConcreteName = $attributes[self::KEY_NAME];
        }

        if(array_key_exists(self::KEY_UNIT, $attributes) === true){
            $localizedProductConcreteName = sprintf(
                '%s%s%s',
                $localizedProductConcreteName,
                self::DELIMITER,
                $attributes[self::KEY_UNIT]
            );
        }

        return $localizedProductConcreteName;
    }
}
