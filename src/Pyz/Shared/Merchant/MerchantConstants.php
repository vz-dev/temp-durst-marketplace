<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 09:42
 */

namespace Pyz\Shared\Merchant;


interface MerchantConstants
{
    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a branch
     */
    public const RESOURCE_TYPE_BRANCH = 'RESOURCE_TYPE_BRANCH';

    /**
     * Name of branch type inside Elasticsearch
     */
    public const BRANCH_SEARCH_TYPE = 'branch';


    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a payment provider
     */
    public const RESOURCE_TYPE_PAYMENT_PROVIDER = 'RESOURCE_TYPE_PAYMENT_PROVIDER';

    /**
     * Name of payment provider type inside Elasticsearch
     */
    public const PAYMENT_PROVIDER_SEARCH_TYPE = 'payment_provider';
}