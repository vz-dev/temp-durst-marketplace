<?php
/**
 * Durst - project - DiscountController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.09.20
 * Time: 09:11
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DiscountController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method \Pyz\Yves\AppRestApi\AppRestApiFactory getFactory()
 */
class DiscountController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkValidVoucherAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createDiscountRequestHandler()
                    ->handleJson(
                        $content
                    )
            );
    }
}
