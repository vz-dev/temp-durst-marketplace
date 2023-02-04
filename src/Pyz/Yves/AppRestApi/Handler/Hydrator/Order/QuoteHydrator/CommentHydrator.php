<?php
/**
 * Durst - project - CommentHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.09.18
 * Time: 15:14
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;

class CommentHydrator implements QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, \stdClass $requestObject)
    {
        if(isset($requestObject->{Request::KEY_MESSAGE})){
            $quoteTransfer->setComment(trim($requestObject->{Request::KEY_MESSAGE}));
        }
    }
}