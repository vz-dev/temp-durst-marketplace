<?php
/**
 * Durst - project - PostMerchantPriceSavePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 14:12
 */

namespace Pyz\Zed\MerchantPrice\Communication\Plugin;


use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;

interface PostMerchantPriceSavePluginInterface
{
    /**
     * @param MerchantPrice $merchantPrice
     * @return void
     */
    public function postMerchantPriceSave(MerchantPrice $merchantPrice);
}