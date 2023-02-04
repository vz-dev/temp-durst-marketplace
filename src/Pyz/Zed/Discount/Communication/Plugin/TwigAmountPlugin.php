<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-05
 * Time: 14:34
 */

namespace Pyz\Zed\Discount\Communication\Plugin;


use Generated\Shared\Transfer\SpyDiscountEntityTransfer;
use Spryker\Shared\Twig\TwigFunction;

/**
 * Class TwigAmountPlugin
 * @package Pyz\Zed\Discount\Communication\Plugin
 */
class TwigAmountPlugin extends TwigFunction
{

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return 'formatDiscountAmount';
    }

    /**
     * @return \Closure
     */
    public function getFunction()
    {
        return function(SpyDiscountEntityTransfer $discountEntityTransfer, array $calculatorPlugins) {
            $calculatorPlugin = $calculatorPlugins[$discountEntityTransfer->getCalculatorPlugin()];

            if (count($discountEntityTransfer->getSpyDiscountAmounts()) === 0) {
                return $calculatorPlugin
                    ->getFormattedAmount($discountEntityTransfer->getAmount());
            }

            $rowTemplate = 'Netto: %s<br />Brutto: %s<br />';
            $row = '';

            foreach ($discountEntityTransfer->getSpyDiscountAmounts() as $spyDiscountAmount) {
                $netAmount = '-';
                $grossAmount = '-';
                $currencyCode = $spyDiscountAmount
                    ->getCurrency()
                    ->getCode();

                if ($spyDiscountAmount->getNetAmount() !== null) {
                    $netAmount = $calculatorPlugin
                        ->getFormattedAmount(
                            $spyDiscountAmount->getNetAmount(),
                            $currencyCode
                        );
                }

                if ($spyDiscountAmount->getGrossAmount() !== null) {
                    $grossAmount = $calculatorPlugin
                        ->getFormattedAmount(
                            $spyDiscountAmount->getGrossAmount(),
                            $currencyCode
                        );
                }

                $row .= sprintf(
                    $rowTemplate,
                    $netAmount,
                    $grossAmount
                );
            }

            return $row;
        };
    }
}