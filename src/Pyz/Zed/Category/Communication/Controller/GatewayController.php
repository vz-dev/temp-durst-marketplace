<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 11:33
 */

namespace Pyz\Zed\Category\Communication\Controller;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Pyz\Zed\Category\Business\CategoryFacadeInterface;
use Pyz\Zed\Category\Communication\CategoryCommunicationFactory;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\Category\Communication\Controller
 * @method CategoryFacadeInterface getFacade()
 * @method CategoryCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return TransferInterface
     */
    public function getCategoryListAction(AppApiRequestTransfer $requestTransfer)
    {
        $currentLocale = $this
            ->getFactory()
            ->getCurrentLocale();

        $categoryArray = $this
            ->getFacade()
            ->getCategoryList($currentLocale->getIdLocale());

        return (new AppApiResponseTransfer())
            ->setCategoryList(new \ArrayObject($categoryArray));
    }
}