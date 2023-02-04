<?php
/**
 * Durst - project - PaymentController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.01.19
 * Time: 10:49
 */

namespace Pyz\Yves\AppRestApi\Controller;

use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\Exception\JsonMalformedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method \Pyz\Yves\AppRestApi\AppRestApiFactory getFactory()
 */
class PaymentController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function statusByOrderRefAction(Request $request)
    {
        $content = $request->getContent();

        $response = [];
        if (empty($content) !== true) {
            try {
                $response = $this
                    ->getFactory()
                    ->createPaymentStatusByOrderRefRequestHandler()
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
