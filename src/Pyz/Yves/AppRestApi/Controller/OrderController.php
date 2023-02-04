<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 28.02.18
 * Time: 10:13
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Exception\JsonMalformedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class OrderController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function persistAction(Request $request)
    {
        $content = $request->getContent();

        $response = [];
        if(empty($content) !== true){
            try {
                $response = $this
                    ->getFactory()
                    ->createOrderRequestHandler()
                    ->handleJson($content);
            } catch (JsonMalformedException $e) {
                return $this
                    ->jsonResponse([
                        'error' => true,
                        'message' => $e->getMessage(),
                    ], JsonMalformedException::STATUS_CODE);
            }
        }

        return $this
            ->jsonResponse($response);
    }
}