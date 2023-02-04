<?php
/**
 * Durst - project - VoucherController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 16:40
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VoucherController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class VoucherController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Yves\AppRestApi\Exception\InvalidJsonException
     * @throws \Spryker\Shared\ZedRequest\Client\Exception\RequestException
     */
    public function getByCodeAction(Request $request)
    {
        $content = $request->getContent();

        return $this
            ->jsonResponse($this
                ->getFactory()
                ->createVoucherRequestHandler()
                ->handleJson($content));
    }
}