<?php
/**
 * Durst - project - TwigCartDiscountGroupAmountPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.04.21
 * Time: 11:14
 */

namespace Pyz\Zed\Discount\Communication\Plugin;

use Pyz\Zed\MerchantCenter\Business\Transfer\MCCartDiscountGroupDiscount;
use Spryker\Shared\Twig\TwigFunction;

class TwigCartDiscountGroupAmountPlugin extends TwigFunction
{
    protected const TWIG_ART_DISCOUNT_GROUP_FUNCTIONNAME = 'formatCartDiscountGroupAmount';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function getFunctionName(): string
    {
        return static::TWIG_ART_DISCOUNT_GROUP_FUNCTIONNAME;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Closure
     */
    protected function getFunction(): \Closure
    {
        return function (MCCartDiscountGroupDiscount $cartDiscountGroupDiscount, array $calculatorPlugins) {
            $calculatorPlugin = $calculatorPlugins[$cartDiscountGroupDiscount->getCalculatorPlugin()];

            $rowTemplate = 'vorher Preis: <s>%s</s><br />nachher Preis: %s<br />Rabatt Netto: %s<br />Rabatt Brutto: %s<br />';
            $row = '';

            /* @var $moneyCollection \Generated\Shared\Transfer\MoneyValueTransfer */
            foreach ($cartDiscountGroupDiscount->getMoneyCollection() as $moneyCollection) {
                $price = '-';
                $newPrice = '-';
                $netAmount = '-';
                $grossAmount = '-';

                $currencyCode = $moneyCollection
                    ->getCurrency()
                    ->getCode();

                if ($moneyCollection->getNetAmount() !== null) {
                    $netAmount = $calculatorPlugin
                        ->getFormattedAmount(
                            $moneyCollection->getNetAmount(),
                            $currencyCode
                        );
                }

                if ($moneyCollection->getGrossAmount() !== null) {
                    $grossAmount = $calculatorPlugin
                        ->getFormattedAmount(
                            $moneyCollection->getGrossAmount(),
                            $currencyCode
                        );
                }

                if ($cartDiscountGroupDiscount->getOriginalPrice() !== null) {
                    $price = $calculatorPlugin
                        ->getFormattedAmount(
                            $cartDiscountGroupDiscount->getOriginalPrice(),
                            $currencyCode
                        );

                    $newPrice = $calculatorPlugin
                        ->getFormattedAmount(
                            (
                                $cartDiscountGroupDiscount->getOriginalPrice() - $moneyCollection->getGrossAmount()
                            ),
                            $currencyCode
                        );
                }

                $row .= sprintf(
                    $rowTemplate,
                    $price,
                    $newPrice,
                    $netAmount,
                    $grossAmount
                );
            }

            return $row;
        };
    }
}
