<?php
/**
 * Durst - project - VoucherModel.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 08:33
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface;
use Spryker\Shared\Price\PriceConfig;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToCurrencyInterface;

class VoucherModel implements VoucherModelInterface
{
    /**
     * @var \Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface
     */
    protected $calculationFacade;

    /**
     * @var \Spryker\Zed\Discount\Dependency\Facade\DiscountToCurrencyInterface
     */
    protected $currencyFacade;

    /**
     * VoucherModel constructor.
     * @param \Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface $calculationFacade
     * @param \Spryker\Zed\Discount\Dependency\Facade\DiscountToCurrencyInterface $currencyFacade
     */
    public function __construct(
        DiscountToCalculationBridgeInterface $calculationFacade,
        DiscountToCurrencyInterface $currencyFacade
    )
    {
        $this->calculationFacade = $calculationFacade;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addVoucherCodeToQuote(
        DiscountApiRequestTransfer $discountApiRequestTransfer
    ): QuoteTransfer
    {
        $quote = $this
            ->createQuote(
                $discountApiRequestTransfer
            );

        $voucherDiscount = (new DiscountTransfer())
            ->setVoucherCode(
                $discountApiRequestTransfer
                    ->getVoucherCode()
            );

        $quote = $quote
            ->addVoucherDiscount(
                $voucherDiscount
            );

        return $this
            ->calculationFacade
            ->recalculateQuote(
                $quote
            );
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuote(DiscountApiRequestTransfer $discountApiRequestTransfer): QuoteTransfer
    {
        return (new QuoteTransfer())
            ->setFkBranch(
                $discountApiRequestTransfer
                    ->getIdBranch()
            )
            ->setFkConcreteTimeSlot(
                $discountApiRequestTransfer
                    ->getIdTimeSlot()
            )
            ->setItems(
                $discountApiRequestTransfer
                    ->getItems()
            )
            ->setShippingAddress(
                $discountApiRequestTransfer
                    ->getShippingAddress()
            )
            ->setPriceMode(
                PriceConfig::PRICE_MODE_GROSS
            )
            ->setCurrency(
                $this
                    ->currencyFacade
                    ->getCurrent()
            );
    }
}
