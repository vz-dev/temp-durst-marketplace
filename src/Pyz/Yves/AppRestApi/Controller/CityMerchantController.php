<?php
/**
 * Durst - project - CityMerchantController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-17
 * Time: 15:49
 */

namespace Pyz\Yves\AppRestApi\Controller;


use Exception;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Exception\InvalidVersionException;
use Pyz\Yves\AppRestApi\Handler\Json\Request\CityMerchantKeyRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CityMerchantController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method AppRestApiFactory getFactory()
 */
class CityMerchantController extends AbstractController
{
    public const KEY_GET_ZIP_CODE = 'zipCode';
    public const KEY_BRANCH_CODE = 'branchCode';
    public const KEY_VERSION = 'version';

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
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getMerchantsByZipCodeAction(Request $request): JsonResponse
    {
        $version = $request->get(self::KEY_VERSION);

        $this->version = ($version !== null && $version !== '')
            ? $request->get(self::KEY_VERSION)
            : self::DEFAULT_VERSION;

        $this->checkVersion();

        $content = $this->getRequestContent($request);

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createCityMerchantRequestHydrator()
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
     * @param Request $request
     * @return string
     */
    protected function getRequestContent(Request $request): string
    {
        if ($this->version === self::VERSION_1) {
            $zipCode = $request->get(self::KEY_GET_ZIP_CODE, '');
            $branchCode = $request->get(self::KEY_BRANCH_CODE, '');

            return json_encode(
                [
                    CityMerchantKeyRequestInterface::KEY_ZIP_CODE => $zipCode,
                    CityMerchantKeyRequestInterface::KEY_BRANCH_CODE => $branchCode
                ]
            );
        }

        $content = json_decode($request->getContent(), true);

        if (!isset($content['zip_code'])) {
            $content['zip_code'] = '';
        }

        if (!isset($content['branch_code'])) {
            $content['branch_code']= '';
        }

        return json_encode($content);
    }
}
