<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 11.06.19
 * Time: 10:22
 */

namespace Pyz\Yves\AppRestApi\Controller;

use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Spryker\Yves\Kernel\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DeliveryAreaController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class DeliveryAreaController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCityNameByZipCodeAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createCityRequestHandler()
                    ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBranchDeliversAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createDeliveryAreaRequestHandler()
                    ->handleJson($content)
            );
    }
}
