<?php
/**
 * Durst - project - LogController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.02.19
 * Time: 13:30
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Controller;


use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class LogController
 * @package Pyz\Zed\HeidelpayRest\Communication\Controller
 * @method \Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory getFactory()
 */
class LogController extends AbstractController
{
    public const LOG_INDEX_URL = '/heidelpay-rest/log';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createPaymentLogTable();

        return $this
            ->viewResponse([
                'table' => $table->render(),
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
            ->createPaymentLogTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}