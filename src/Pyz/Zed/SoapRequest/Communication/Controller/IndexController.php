<?php
/**
 * Durst - project - IndexController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:36
 */

namespace Pyz\Zed\SoapRequest\Communication\Controller;

use Pyz\Zed\SoapRequest\Business\SoapRequestFacadeInterface;
use Pyz\Zed\SoapRequest\Communication\SoapRequestCommunicationFactory;
use Pyz\Zed\SoapRequest\Communication\Table\SoapRequestTable;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexController
 * @package Pyz\Zed\SoapRequest\Communication\Controller
 * @method SoapRequestCommunicationFactory getFactory()
 * @method  SoapRequestFacadeInterface getFacade()
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
            ->createSoapRequestTable();

        return $this
            ->viewResponse([
                'soapRequests' => $table->render()
            ]);
    }

    /**
     * @return JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createSoapRequestTable();

        return $this
            ->jsonResponse(
                $table->fetchData()
            );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function viewAction(Request $request): array
    {
        $idSoapRequest = $this
            ->castId(
                $request->get(
                    SoapRequestTable::PARAM_ID_SOAP_REQUEST
                )
            );

        $soapRequestTransfer = $this
            ->getFacade()
            ->getSoapRequestById($idSoapRequest);

        return $this
            ->viewResponse([
                'idSoapRequest' => $idSoapRequest,
                'soapRequest' => $soapRequestTransfer
            ]);
    }
}
