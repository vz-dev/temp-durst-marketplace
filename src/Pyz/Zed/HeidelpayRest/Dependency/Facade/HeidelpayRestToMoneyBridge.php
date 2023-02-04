<?php
/**
 * Durst - project - HeidelpayRestToMoneyBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.01.19
 * Time: 12:30
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class HeidelpayRestToMoneyBridge implements HeidelpayRestToMoneyBridgeInterface
{
    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * HeidelpayRestToMoneyBridge constructor.
     *
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     */
    public function __construct(MoneyFacadeInterface $moneyFacade)
    {
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $value
     * @return float
     */
    public function convertIntegerToDecimal(int $value): float
    {
        return $this
            ->moneyFacade
            ->convertIntegerToDecimal($value);
    }

    /**
     * {@inheritdoc}
     *
     * @param float $value
     * @return int
     */
    public function convertDecimalToInteger(float $value): int
    {
        return $this
            ->moneyFacade
            ->convertDecimalToInteger($value);
    }
}
