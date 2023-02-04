<?php
/**
 * Durst - project - ProductAbstractNameGenerator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.07.20
 * Time: 09:33
 */

namespace Pyz\Zed\Product\Business\Product\NameGenerator;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Product\Business\Product\NameGenerator\ProductAbstractNameGenerator as SprykerProductAbstractNameGenerator;

class ProductAbstractNameGenerator extends SprykerProductAbstractNameGenerator
{
    protected const KEY_NAME = 'name';

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    public function getLocalizedProductAbstractName(ProductAbstractTransfer $productAbstractTransfer, LocaleTransfer $localeTransfer)
    {
        foreach ($productAbstractTransfer->getLocalizedAttributes() as $localizedAttribute) {
            if ($localizedAttribute->getLocale()->getIdLocale() === $localeTransfer->getIdLocale()) {
                if($localizedAttribute->getName() !== null && $localizedAttribute->getName() !== ''){
                    return $localizedAttribute->getName();
                }
            }
        }

        $localizedProductAbstractName = $productAbstractTransfer->getSku();

        $attributes = $productAbstractTransfer->getAttributes();
        if(array_key_exists(self::KEY_NAME, $attributes) === true){
            $localizedProductAbstractName = $attributes[self::KEY_NAME];
        }

        return $localizedProductAbstractName;
    }
}
