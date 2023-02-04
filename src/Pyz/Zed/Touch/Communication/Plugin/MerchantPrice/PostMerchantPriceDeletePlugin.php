<?php
/**
 * Durst - project - PostMerchantPriceDeletePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 14:16
 */

namespace Pyz\Zed\Touch\Communication\Plugin\MerchantPrice;

use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceDeletePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostMerchantPriceDeletePlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\MerchantPrice
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PostMerchantPriceDeletePlugin extends AbstractPlugin implements PostMerchantPriceDeletePluginInterface
{
    /**
     * adds a delete touch event to the touch table for the given merchant price
     *
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     *
     * @return void
     */
    public function postMerchantPriceDelete(MerchantPrice $merchantPrice)
    {
        $this->getFacade()->touchDeleted(MerchantPriceConstants::RESOURCE_TYPE_PRICE, $merchantPrice->getIdPrice());
    }
}
