<?php
/**
 * Durst - project - ConcreteTimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 13:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;
use stdClass;

class ConcreteTimeSlotHydrator implements QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param stdClass $requestObject
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, stdClass $requestObject)
    {
        if($quoteTransfer->getUseFlexibleTimeSlots() !== true) {
            $quoteTransfer->setFkConcreteTimeSlot($requestObject->{Request::KEY_ID_CONCRETE_TIME_SLOT});
        }
    }
}
