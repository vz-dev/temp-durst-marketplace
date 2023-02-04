<?php
/**
 * Durst - project - GrandTotalIsNegativeException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.01.20
 * Time: 10:27
 */

namespace Pyz\Zed\Calculation\Business\Exception;


class GrandTotalIsNegativeException extends CalculationException
{
    public const MESSAGE = 'Der Rechnunsbetrag ist negativ.';
}
