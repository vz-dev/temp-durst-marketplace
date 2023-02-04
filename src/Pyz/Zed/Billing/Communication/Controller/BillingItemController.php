<?php
/**
 * Durst - project - BillingItemController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-26
 * Time: 15:15
 */

namespace Pyz\Zed\Billing\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class BillingItemController
 * @package Pyz\Zed\Billing\Communication\Controller
 * @method \Pyz\Zed\Billing\Communication\BillingCommunicationFactory getFactory()
 */
class BillingItemController extends AbstractController
{
    /**
     *
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createBillingItemTable();

        return [
            'billingItems' => $table->render(),
        ];
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createBillingItemTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}
