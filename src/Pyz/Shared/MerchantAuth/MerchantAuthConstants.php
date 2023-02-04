<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.11.17
 * Time: 15:44
 */

namespace Pyz\Shared\MerchantAuth;


interface MerchantAuthConstants
{
    const MERCHANT_AUTH_SESSION_KEY = 'merchantAuth';
    const MERCHANT_AUTH_CURRENT_MERCHANT_KEY = '%s:currentMerchant:%s';
    const AUTHORIZATION_WILDCARD = '*';
    const DAY_IN_SECONDS = 86400;
    const MERCHANT_AUTH_TOKEN = 'Merchant-Auth-Token';
}