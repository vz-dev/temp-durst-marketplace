<?php
/**
 * Durst - project - QuoteHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 12:58
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, \stdClass $requestObject);
}