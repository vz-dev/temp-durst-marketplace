<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.05.18
 * Time: 13:34
 */

namespace Pyz\Zed\MerchantPrice\Communication\Controller;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\MerchantPrice\Communication\Controller
 * @method MerchantPriceFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCatalogForBranchesAction(AppApiRequestTransfer $requestTransfer)
    {
        $catalog = $this
            ->getFacade()
            ->getCatalogForBranches($requestTransfer->getBranchIds());


        return (new AppApiResponseTransfer())
            ->setCategories($catalog);
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getCatalogProductForBranchBySkuAction(AppApiRequestTransfer $requestTransfer)
    {
        $product = $this
            ->getFacade()
            ->getCatalogProductForBranchBySku($requestTransfer->getIdBranch(), $requestTransfer->getSku());

        return (new AppApiResponseTransfer())
            ->setProduct($product);
    }
}
