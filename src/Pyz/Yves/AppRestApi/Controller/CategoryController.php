<?php
/**
 * Durst - project - CategoryController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 12:00
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class CategoryController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Yves\AppRestApi\Exception\InvalidJsonException
     */
    public function getCategoryListAction(Request $request)
    {
        $content = $request->getContent();

        return $this
            ->jsonResponse($this
                ->getFactory()
                ->createCategoryRequestHandler()
                ->handleJson($content));
    }
}