<?php
/**
 * Durst - project - MerchantTimeSlotController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-24
 * Time: 11:41
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MerchantTimeSlotController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class MerchantTimeSlotController extends AbstractController
{
    public const KEY_GET_VERSION = 'version';

    public const VERSION_1 = 'v1';
    public const VERSION_2 = 'v2';

    public const VALID_VERSIONS = [
        self::VERSION_1,
        self::VERSION_2
    ];

    public const DEFAULT_VERSION = self::VERSION_1;

    /**
     * @var string
     */
    protected $version;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function getForMerchantAction(Request $request)
    {
        if ($request->get(self::KEY_GET_VERSION) == null
            || $request->get(self::KEY_GET_VERSION) == ''
        ) {
            $this->version = self::DEFAULT_VERSION;
        } else {
            $this->version = $request->get(self::KEY_GET_VERSION);
        }

        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createMerchantTimeSlotRequestHandler()
                    ->handleJson($content, $this->version)
            );
    }
}
