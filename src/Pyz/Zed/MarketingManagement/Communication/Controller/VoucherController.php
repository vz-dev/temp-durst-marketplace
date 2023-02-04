<?php
/**
 * Durst - project - VoucherController.php.
 *
 * Initial version by:
 * User: Zhaklina Basha, <zhaklina.basha@durst.shop>
 * Date: 2021-07-05
 * Time: 09:30
 */

namespace Pyz\Zed\MarketingManagement\Communication\Controller;


use Pyz\Zed\MarketingManagement\Communication\MarketingManagementCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DiscountController
 * @package Pyz\Zed\MarketingManagement\Communication\Controller
 * @method MarketingManagementCommunicationFactory getFactory()
 */
class VoucherController extends AbstractController
{
    /**
     * @param Request $request
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction(Request $request)
    {
        $branchesTable = $this
            ->getFactory()
            ->createVoucherTable();

        return [
            'vouchers' => $branchesTable->render(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createVoucherTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}
