<?php
/**
 * Durst - project - IndexController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 12:16
 */

namespace Pyz\Zed\HttpRequest\Communication\Controller;

use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;
use Pyz\Zed\HttpRequest\Communication\HttpRequestCommunicationFactory;
use Pyz\Zed\HttpRequest\Communication\Table\HttpRequestTable;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexController
 * @package Pyz\Zed\HttpRequest\Communication\Controller
 * @method HttpRequestCommunicationFactory getFactory()
 * @method HttpRequestFacadeInterface getFacade()
 */
class IndexController extends AbstractController
{
    /**
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createHttpRequestTable();

        return $this
            ->viewResponse([
                'httpRequests' => $table->render()
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createHttpRequestTable();

        return $this
            ->jsonResponse(
                $table->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function viewAction(Request $request): array
    {
        $idHttpRequest = $this
            ->castId(
                $request->get(
                    HttpRequestTable::PARAM_ID_HTTP_REQUEST
                )
            );

        $httpRequestTransfer = $this
            ->getFacade()
            ->getHttpRequestById($idHttpRequest);

        return $this
            ->viewResponse([
                'idHttpRequest' => $idHttpRequest,
                'httpRequest' => $httpRequestTransfer
            ]);
    }
}
