<?php
/**
 * Durst - project - TourController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.10.19
 * Time: 13:58
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class TourController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method \Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory getFactory()
 */
class TourController extends AbstractController
{
    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createTourTable();

        return $this
            ->viewResponse([
                'tourTable' => $table->render(),
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createTourTable();

        return $this
            ->jsonResponse(
                $table->fetchData()
            );
    }
}
