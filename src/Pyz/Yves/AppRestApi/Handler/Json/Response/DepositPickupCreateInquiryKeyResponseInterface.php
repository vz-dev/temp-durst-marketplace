<?php

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface DepositPickupCreateInquiryKeyResponseInterface
{
    public const KEY_ERROR = 'error';
    public const KEY_ERROR_CODE = 'code';
    public const KEY_ERROR_MESSAGE = 'message';

    public const KEY_ERRORS = 'errors';

    public const KEY_IS_SUCCESS = 'is_success';
}
