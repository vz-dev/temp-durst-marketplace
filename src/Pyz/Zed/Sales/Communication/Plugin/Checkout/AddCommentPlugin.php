<?php
/**
 * Durst - project - AddCommentPlugin.php
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 01.10.18
 * Time: 15:14
 */

namespace Pyz\Zed\Sales\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CommentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class AddCommentPlugin
 * @package Pyz\Zed\Sales\Communication\Plugin\Checkout
 * @method SalesFacadeInterface getFacade()
 */
class AddCommentPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        if($quoteTransfer->getComment() !== null){
            $commentTransfer = $this->createCommentTransferFromQuoteTransfer($quoteTransfer, $saveOrderTransfer);

            $this
                ->getFacade()
                ->saveComment($commentTransfer);
        }
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return CommentTransfer
     */
    protected function createCommentTransferFromQuoteTransfer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer) : CommentTransfer
    {
        $commentTransfer = new CommentTransfer();
        $commentTransfer->setMessage($quoteTransfer->getComment());
        $commentTransfer->setUsername($quoteTransfer->getCustomer()->getFirstName().' '.$quoteTransfer->getCustomer()->getLastName());
        $commentTransfer->setFkSalesOrder($saveOrderTransfer->getIdSalesOrder());
        $commentTransfer->setType(SalesConstants::COMMENT_TYPE_CUSTOMER);

        return $commentTransfer;
    }
}