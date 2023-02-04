<?php
/**
 * Durst - project - GMTimeSlotHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.01.22
 * Time: 20:06
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;
use stdClass;

class GMTimeSlotHydrator implements QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param stdClass $requestObject
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, stdClass $requestObject)
    {
        if($quoteTransfer->getUseFlexibleTimeSlots() === true){
            $quoteTransfer->setStartTime(trim($requestObject->{Request::KEY_ID_TIME_SLOT_START}));
            $quoteTransfer->setEndTime(trim($requestObject->{Request::KEY_ID_TIME_SLOT_END}));
        }
    }
}
