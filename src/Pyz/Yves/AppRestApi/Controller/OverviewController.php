<?php
/**
 * Durst - project - OverviewController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 11:42
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Exception;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OverviewKeyRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OverviewController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class OverviewController extends AbstractController
{
    public const KEY_GET_TIME_SLOT_ID = 'time-slot-id';

    public const KEY_GET_VERSION = 'version';

    public const VERSION_1 = 'v1';
    public const VERSION_2 = 'v2';

    public const VALID_VERSIONS = [
        self::VERSION_1,
        self::VERSION_2,
    ];

    public const DEFAULT_VERSION = self::VERSION_1;

    /**
     * @var string
     */
    protected $version;

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getPriceAndExpenseOverviewAction(Request $request): JsonResponse
    {
        $json = json_decode(
            $request->getContent(),
            true
        );

        if ($request->get(self::KEY_GET_VERSION) == null
            || $request->get(self::KEY_GET_VERSION) == ''
        ) {
            $this->version = self::DEFAULT_VERSION;
        } else {
            $this->version = $request->get(self::KEY_GET_VERSION);
        }

        $json[OverviewKeyRequestInterface::KEY_TIME_SLOT_ID] = (int)$request
            ->get(self::KEY_GET_TIME_SLOT_ID);

        $content = json_encode($json);

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createOverviewRequestHandler()
                    ->handleJson($content, $this->version)
            );
    }
}
