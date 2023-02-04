<?php
/**
 * Durst - project - PostMerchantPriceSavePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.11.18
 * Time: 14:21
 */

namespace Pyz\Zed\Touch\Communication\Plugin\MerchantPrice;

use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\MerchantPrice\Communication\Plugin\PostMerchantPriceSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostMerchantPriceSavePlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\MerchantPrice
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PostMerchantPriceSavePlugin extends AbstractPlugin implements PostMerchantPriceSavePluginInterface
{
    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     *
     * @return void
     */
    public function postMerchantPriceSave(MerchantPrice $merchantPrice)
    {
        $this->getFacade()->touchActive(MerchantPriceConstants::RESOURCE_TYPE_PRICE, $merchantPrice->getIdPrice());
    }
}
