<?php
/**
 * Durst - project - GraphmastersController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 19:25
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Exception;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GraphmastersController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class GraphmastersController extends AbstractController
{
    public const KEY_GET_BRANCH_ID = 'branchId';

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function evaluateTimeSlotsAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createGraphmastersRequestHandler()
                    ->handleJson($content)
            );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSettingsAction(Request $request): JsonResponse
    {

        $content = json_encode(
            [
                self::KEY_GET_BRANCH_ID => (int) $request->get(self::KEY_GET_BRANCH_ID)
            ]
        );

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createGraphmastersSettingsRequestHandler()
                    ->handleJson($content)
            );
    }
}
