<?php

namespace Pyz\Zed\GraphMasters\Communication\Controller;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacade;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method GraphMastersFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    public function evaluateTimeslotsAction(AppApiRequestTransfer $requestTransfer) : AppApiResponseTransfer
    {
        return $this
            ->getFacade()
            ->evaluateTimeSlot($requestTransfer);
    }
}
