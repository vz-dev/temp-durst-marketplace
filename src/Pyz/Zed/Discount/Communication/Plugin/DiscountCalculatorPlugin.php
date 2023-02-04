<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-13
 * Time: 11:53
 */

namespace Pyz\Zed\Discount\Communication\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Spryker\Zed\DiscountCalculationConnector\Communication\Plugin\DiscountCalculatorPlugin as SprykerDiscountCalculatorPlugin;

/**
 * Class DiscountCalculatorPlugin
 * @package Pyz\Zed\Discount\Communication\Plugin
 * @method DiscountFacadeInterface getFacade()
 */
class DiscountCalculatorPlugin extends SprykerDiscountCalculatorPlugin
{

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return array|QuoteTransfer
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        return $this
            ->getFacade()
            ->calculateDiscounts($quoteTransfer);
    }
}