<?php
/**
 * Durst - project - PostMerchantPriceDeletePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 14:14
 */

namespace Pyz\Zed\MerchantPrice\Communication\Plugin;


use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;

interface PostMerchantPriceDeletePluginInterface
{
    /**
     * @param MerchantPrice $merchantPrice
     * @return void
     */
    public function postMerchantPriceDelete(MerchantPrice $merchantPrice);
}