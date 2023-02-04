<?php
/**
 * Durst - project - EnrichtAttributesTranslatorFunctionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.19
 * Time: 13:58
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction;

use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Translator\TranslatorFunction\EnrichAttributes;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\TranslatorFunction\EnrichAttributesTranslatorFunctionPlugin as SprykerEcoEnrichAttributesTranslatorFunctionPlugin;

class EnrichAttributesTranslatorFunctionPlugin extends SprykerEcoEnrichAttributesTranslatorFunctionPlugin
{
    /**
     * @return string
     */
    public function getTranslatorFunctionClassName(): string
    {
        return EnrichAttributes::class;
    }
}
