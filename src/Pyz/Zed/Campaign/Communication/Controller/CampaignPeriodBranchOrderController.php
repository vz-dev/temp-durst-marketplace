<?php
/**
 * Durst - project - CampaignPeriodBranchOrderController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.06.21
 * Time: 16:54
 */

namespace Pyz\Zed\Campaign\Communication\Controller;

use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\CampaignCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CampaignPeriodBranchOrderController
 * @package Pyz\Zed\Campaign\Communication\Controller
 * @method CampaignCommunicationFactory getFactory()
 * @method CampaignFacadeInterface getFacade()
 */
class CampaignPeriodBranchOrderController extends AbstractController
{
    public const URL_LISTING = '/campaign/campaign-period-branch-order/index';
    public const URL_VIEW = '/campaign/campaign-period-branch-order/view';

    public const PARAM_ID_CAMPAIGN_PERIOD_BRANCH_ORDER = 'id-campaign-period-branch-order';

    /**
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderTable();

        return $this
            ->viewResponse(
                [
                    'campaignPeriodBranchOrders' => $table->render()
                ]
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createCampaignPeriodBranchOrderTable();

        return $this
            ->jsonResponse(
                $table
                    ->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function viewAction(Request $request): array
    {
        $idCampaignBranchOrder = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_PERIOD_BRANCH_ORDER)
            );

        $campaignBranchOrder = $this
            ->getFacade()
            ->getCampaignPeriodBranchOrderById(
                $idCampaignBranchOrder
            );

        return $this
            ->viewResponse(
                [
                    'campaignPeriodBranchOrder' => $campaignBranchOrder
                ]
            );
    }
}
