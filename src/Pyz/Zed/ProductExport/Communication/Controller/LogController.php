<?php
/**
 * Durst - project - LogController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 12:31
 */

namespace Pyz\Zed\ProductExport\Communication\Controller;


use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class LogController
 * @package Pyz\Zed\ProductExport\Communication\Controller
 * @method \Pyz\Zed\ProductExport\Communication\ProductExportCommunicationFactory getFactory()
 */
class LogController extends AbstractController
{
    public const LOG_INDEX_URL = '/product-export/log';

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createProductExportLogTable();

        return $this
            ->viewResponse(
                [
                    'table' => $table->render()
                ]
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createProductExportLogTable();

        return $this
            ->jsonResponse(
                $table
                    ->fetchData()
            );
    }
}
