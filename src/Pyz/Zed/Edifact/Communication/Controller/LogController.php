<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 15:25
 */

namespace Pyz\Zed\Edifact\Communication\Controller;


use Pyz\Zed\Edifact\Communication\EdifactCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * Class LogController
 * @package Pyz\Zed\Edifact\Communication\Controller
 * @method EdifactCommunicationFactory getFactory()
 */
class LogController extends AbstractController
{
    public const LOG_INDEX_URL = '/edifact/log';

    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createEdifactExportLogTable();

        return $this
            ->viewResponse([
                'table' => $table->render()
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createEdifactExportLogTable();

        return $this
            ->jsonResponse(
                $table->fetchData()
            );
    }
}