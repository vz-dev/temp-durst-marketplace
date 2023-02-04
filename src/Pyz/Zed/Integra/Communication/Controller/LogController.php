<?php
/**
 * Durst - project - LogController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 18.11.20
 * Time: 17:29
 */

namespace Pyz\Zed\Integra\Communication\Controller;


use Pyz\Zed\Integra\Business\Model\Log\LoggerInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class LogController
 * @package Pyz\Zed\Integra\Communication\Controller
 * @method \Pyz\Zed\Integra\Communication\IntegraCommunicationFactory getFactory()
 */
class LogController extends AbstractController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        $table = $this
            ->getFactory()
            ->createLogTable();

        return [
            'table' => $table->render(),
        ];
    }

    /**
     * @return JsonResponse
     */
    public function tableAction()
    {
        $table = $this
            ->getFactory()
            ->createLogTable();

        return $this->jsonResponse(
            $table->fetchData()
        );
    }
}
