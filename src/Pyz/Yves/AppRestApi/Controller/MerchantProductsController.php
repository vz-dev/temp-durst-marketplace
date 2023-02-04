<?php
/**
 * Durst - project - MerchantProductsController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 12:21
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Exception;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Exception\InvalidVersionException;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductsKeyRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MerchantProductsController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class MerchantProductsController extends AbstractController
{
    public const KEY_GET_BRANCH_ID = 'branchId';
    public const KEY_GET_VERSION = 'version';

    public const VERSION_1 = 'v1';
    public const VERSION_2 = 'v2';
    public const VERSION_3 = 'v3';

    public const VALID_VERSIONS = [
        self::VERSION_1,
        self::VERSION_2,
        self::VERSION_3
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
    public function getProductsByBranchIdAction(Request $request): JsonResponse
    {
        $this->version = $request->get(self::KEY_GET_VERSION) !== ''
            ? $request->get(self::KEY_GET_VERSION)
            : self::DEFAULT_VERSION;

        $this->checkVersion();

        $idBranch = (int) $request->get(self::KEY_GET_BRANCH_ID);

        $content = $this->createRequestContent($idBranch);

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createMerchantProductsRequestHydrator($this->version)
                    ->handleJson($content, $this->version)
            );
    }

    /**
     * @throws InvalidVersionException
     */
    protected function checkVersion(): void
    {
        if (!in_array($this->version, self::VALID_VERSIONS)) {
            throw new InvalidVersionException(
                sprintf(InvalidVersionException::MESSAGE, $this->version)
            );
        }
    }

    /**
     * @param int $idBranch
     * @return string
     */
    protected function createRequestContent(int $idBranch): string
    {
        if ($this->version === self::VERSION_1) {
            $key = MerchantProductsKeyRequestInterface::KEY_MERCHANT_ID;
        }

        if ($this->version === self::VERSION_2 || $this->version === self::VERSION_3) {
            $key = MerchantProductsKeyRequestInterface::KEY_BRANCH_ID;
        }

        return json_encode([$key => $idBranch]);
    }
}
