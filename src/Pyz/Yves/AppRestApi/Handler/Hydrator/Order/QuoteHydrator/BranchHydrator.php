<?php
/**
 * Durst - project - BranchHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 13:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;

class BranchHydrator implements QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, \stdClass $requestObject)
    {
        $quoteTransfer->setFkBranch($requestObject->{Request::KEY_ID_BRANCH});
    }
}