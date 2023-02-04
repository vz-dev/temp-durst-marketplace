<?php
/**
 * Durst - project - DiscountController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-12
 * Time: 21:20
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
class DiscountController extends AbstractController
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
            ->createDiscountTable();

        return [
            'discounts' => $branchesTable->render(),
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
            ->createDiscountTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}
