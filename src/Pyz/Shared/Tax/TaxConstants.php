<?php
/**
 * Durst - project - TaxConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 08:52
 */

namespace Pyz\Shared\Tax;

use Spryker\Shared\Tax\TaxConstants as SprykerTaxConstants;


interface TaxConstants extends SprykerTaxConstants
{
    const TAX_CORONA_DEADLINE = 'TAX_CORONA_DEADLINE';
    const TAX_CORONA_TAX_RATE = 'TAX_CORONA_TAX_RATE';
    const TAX_CORONA_DEADLINE_END = 'TAX_CORONA_DEADLINE_END';
}
