<?php

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;

interface DepositPickupCreateInquiryKeyRequestInterface
{
    public const KEY_BRANCH_ID = 'branch_id';
    public const KEY_NAME = 'name';
    public const KEY_ADDRESS = 'address';
    public const KEY_EMAIL = 'email';
    public const KEY_PHONE_NUMBER = 'phone_number';
    public const KEY_PREFERRED_DATE = 'preferred_date';
    public const KEY_MESSAGE = 'message';
}
