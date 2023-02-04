<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 10:18
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BranchController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class BranchController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Yves\AppRestApi\Exception\InvalidJsonException
     */
    public function getByZipCodeAction(Request $request)
    {
        $content = $request->getContent();

        return $this
            ->jsonResponse($this
                ->getFactory()
                ->createBranchRequestHandler()
                ->handleJson($content));
    }
}