<?php
/**
 * Durst - project - BillingPeriodController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 11:50
 */

namespace Pyz\Zed\Billing\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BillingPeriodController
 * @package Pyz\Zed\Billing\Communication\Controller
 * @method \Pyz\Zed\Billing\Communication\BillingCommunicationFactory getFactory()
 * @method \Pyz\Zed\Billing\Business\BillingFacadeInterface getFacade()
 */
class BillingPeriodController extends AbstractController
{
    public const URL_DETAIL = '/billing/billing-period/detail';
    public const PARAM_ID_BILLING_PERIOD = 'id-billing-period';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createBillingPeriodTable();

        return [
            'billingPeriods' => $table->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createBillingPeriodTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function detailAction(Request $request)
    {
        $idBillingPeriod = $this
            ->castId($request->get(self::PARAM_ID_BILLING_PERIOD));

        $billingPeriodTransfer = $this
            ->getFacade()
            ->getBillingPeriodById($idBillingPeriod);

        return $this
            ->viewResponse([
               'billingPeriod' => $billingPeriodTransfer,
            ]);
    }
}
