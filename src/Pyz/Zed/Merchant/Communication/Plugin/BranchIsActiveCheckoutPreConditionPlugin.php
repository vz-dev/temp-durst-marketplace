<?php
/**
 * Durst - project - BranchIsActiveCheckoutPreConditionPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:57
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchIsActiveCheckoutPreConditionPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin
 *
 * @method MerchantFacadeInterface getFacade()
 */
class BranchIsActiveCheckoutPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkCondition(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    )
    {
        return $this
            ->getFacade()
            ->validateBranchForCheckout(
                $quoteTransfer,
                $checkoutResponseTransfer
            );
    }
}
