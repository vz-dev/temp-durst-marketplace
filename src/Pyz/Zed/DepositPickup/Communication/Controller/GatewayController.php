<?php

namespace Pyz\Zed\DepositPickup\Communication\Controller;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Pyz\Zed\DepositPickup\Business\DepositPickupFacade;
use Spryker\Shared\ErrorHandler\ErrorLogger;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;
use Throwable;

/**
 * @method DepositPickupFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    const INQUIRY_PROCESSING_ERROR_CODE = 'INQUIRY_PROCESSING_ERROR';
    const INQUIRY_PROCESSING_ERROR_MESSAGE = 'Inquiry could not be processed. See Zed exception log for details.';

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function saveInquiryAction(AppApiRequestTransfer $requestTransfer)
    {
        try {
            $this
                ->getFacade()
                ->saveInquiry($requestTransfer->getDepositPickupInquiry());

            $responseTransfer = (new AppApiResponseTransfer())->setIsSuccess(true);
        } catch (Throwable $exception) {
            $responseTransfer = $this->createResponseWithError($exception);
        }

        return $responseTransfer;
    }

    /**
     * @param Throwable $exception
     *
     * @return AppApiResponseTransfer
     */
    protected function createResponseWithError(Throwable $exception): AppApiResponseTransfer
    {
        $errorTransfer = (new ErrorTransfer())
            ->setCode(static::INQUIRY_PROCESSING_ERROR_CODE)
            ->setMessage(static::INQUIRY_PROCESSING_ERROR_MESSAGE);

        $responseTransfer = (new AppApiResponseTransfer())
            ->setError($errorTransfer)
            ->setIsSuccess(false);

        ErrorLogger::getInstance()->log($exception);

        return $responseTransfer;
    }
}
