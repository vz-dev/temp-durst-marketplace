<?php
/**
 * Durst - project - PimController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.09.18
 * Time: 10:51
 */

namespace Pyz\Zed\MerchantManagement\Communication\Controller;


use Pyz\Zed\MerchantManagement\Communication\MerchantManagementCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class PimController
 * @package Pyz\Zed\MerchantManagement\Communication\Controller
 * @method MerchantManagementCommunicationFactory getFactory()
 */
class PimController extends AbstractController
{
    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function manufacturerAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createManufacturerTable();

        return $this
            ->viewResponse([
                'manufacturerTable' => $table->render(),
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function manufacturerTableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createManufacturerTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function depositAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createDepositTable();

        return $this
            ->viewResponse([
                'depositTable' => $table->render(),
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function depositTableAction()
    {
        $table = $this
            ->getFactory()
            ->createTableFactory()
            ->createDepositTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}