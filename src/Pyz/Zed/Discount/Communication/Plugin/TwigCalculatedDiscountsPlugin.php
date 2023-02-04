<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-25
 * Time: 13:29
 */

namespace Pyz\Zed\Discount\Communication\Plugin;


use Spryker\Shared\Twig\TwigFunction;

class TwigCalculatedDiscountsPlugin extends TwigFunction
{

    /**
     * @return string
     */
    protected function getFunctionName()
    {
        return 'showCalculatedDiscounts';
    }

    /**
     * @return callable|\Closure
     */
    protected function getFunction()
    {
        return function(\ArrayObject $calculatedDiscounts) {
            $discounts = [];

            /* @var $calculatedDiscount \Generated\Shared\Transfer\CalculatedDiscountTransfer */
            foreach ($calculatedDiscounts as $calculatedDiscount) {
                $discountDiscountName = $calculatedDiscount
                    ->getDiscountName();

                if (empty($discountDiscountName) || $discountDiscountName === null) {
                    $discountDiscountName = 'Angebotspreis';
                }

                if (isset($discounts[$discountDiscountName]) === false) {
                    $discounts[$discountDiscountName] = [
                        'name' => $calculatedDiscount->getDiscountName(),
                        'quantity' => $calculatedDiscount->getQuantity(),
                        'unitPrice' => $calculatedDiscount->getUnitAmount(),
                        'sumPrice' => $calculatedDiscount->getSumAmount()
                    ];

                    continue;
                }

                $discounts[$discountDiscountName]['quantity'] += $calculatedDiscount->getQuantity();
                $discounts[$discountDiscountName]['sumPrice'] += $calculatedDiscount->getUnitAmount();
            }

            $row = '';

            foreach ($discounts as $discount) {
                $singlePrice = number_format(($discount['unitPrice'] / 100), 2);
                $totalPrice = number_format(($discount['sumPrice'] / 100), 2);
                $row .= <<<DISCOUNT
<div class="row form-elements alternate">
    <div class="large-down-12 large-1 columns">
        <h4 class="hide-for-large">Anzahl</h4>
        <div class="form-element-wrapper order-plaintext">{$discount['quantity']}x</div>
    </div>
    <div class="large-down-12 large-4 columns">
        <h4 class="hide-for-large">Bezeichnung</h4>
        <div class="form-element-wrapper order-plaintext">{$discount['name']}</div>
    </div>
    <div class="large-down-12 large-3 columns">
        <h4 class="hide-for-large">Bemerkung</h4>
        <div class="form-element-wrapper order-plaintext"> Angebotspreis bereits abgezogen</div>
    </div>
    <div class="large-down-12 large-1 columns">
        <h4 class="hide-for-large">Einzelpreis</h4>
        <div class="form-element-wrapper order-plaintext">- {$singlePrice}€</div>
    </div>
    <div class="large-down-12 large-2 columns">
        <h4 class="hide-for-large">Summe</h4>
        <div class="form-element-wrapper order-plaintext">- {$totalPrice}€</div>
    </div>
</div>
DISCOUNT;

            }

            return $row;
        };
    }
}
