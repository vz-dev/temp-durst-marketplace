<?php
/**
 * Durst - project - HeidelpayRestToMoneyBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.01.19
 * Time: 12:31
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

interface HeidelpayRestToMoneyBridgeInterface
{
    /**
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal(int $value): float;

    /**
     * @param float $value
     *
     * @return int
     */
    public function convertDecimalToInteger(float $value): int;
}
