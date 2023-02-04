<?php
/**
 * Durst - project - BillingToMoneyBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 14:13
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\MoneyTransfer;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class BillingToMoneyBridge implements BillingToMoneyBridgeInterface
{
    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * BillingToMoneyBridge constructor.
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        MoneyFacadeInterface $moneyFacade
    )
    {
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $amount
     * @param string|null $isoCode
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    public function fromInteger(
        int $amount,
        string $isoCode = null
    ): MoneyTransfer
    {
        return $this
            ->moneyFacade
            ->fromInteger(
                $amount,
                $isoCode
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MoneyTransfer $moneyTransfer
     * @return string
     */
    public function formatWithSymbol(
        MoneyTransfer $moneyTransfer
    ): string
    {
        return $this
            ->moneyFacade
            ->formatWithSymbol(
                $moneyTransfer
            );
    }
}
