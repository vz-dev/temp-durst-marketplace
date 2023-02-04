<?php
/**
 * Durst - project - TimeSlotController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.18
 * Time: 16:40
 */

namespace Pyz\Yves\AppRestApi\Controller;

use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TimeSlotController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class TimeSlotController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getForBranchesAction(Request $request)
    {
        $content = $request->getContent();

        return $this
            ->jsonResponse($this
                ->getFactory()
                ->createTimeSlotRequestHandler()
                ->handleJson($content));
    }
}
