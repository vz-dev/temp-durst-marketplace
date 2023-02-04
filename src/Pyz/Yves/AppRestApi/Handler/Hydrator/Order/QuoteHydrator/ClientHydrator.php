<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;

use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;

class ClientHydrator implements QuoteHydratorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, \stdClass $requestObject)
    {
        if(isset($requestObject->{Request::KEY_PLATFORM_CLIENT})){
            $quoteTransfer->setClientPlatform(trim($requestObject->{Request::KEY_PLATFORM_CLIENT}));
        }
        if(isset($requestObject->{Request::KEY_CLIENT_VERSION})){
            $quoteTransfer->setClientVersion(trim($requestObject->{Request::KEY_CLIENT_VERSION}));
        }
        if(isset($requestObject->{Request::KEY_DEVICE_TYPE})){
            $quoteTransfer->setDeviceType(trim($requestObject->{Request::KEY_DEVICE_TYPE}));
        }
    }
}
