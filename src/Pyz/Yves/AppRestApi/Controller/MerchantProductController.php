<?php

namespace Pyz\Yves\AppRestApi\Controller;


use Exception;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductKeyRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method AppRestApiFactory getFactory()
 */
class MerchantProductController extends AbstractController
{
    public const KEY_GET_BRANCH_ID = 'branchId';

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getProductBySkuAction(Request $request): JsonResponse
    {
        $idBranch = (int) $request->get(self::KEY_GET_BRANCH_ID);

        $content = $this->createRequestContent($request, $idBranch);

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createMerchantProductRequestHandler()
                    ->handleRequest($content)
            );
    }

    /**
     * @param Request $request
     * @param int $idBranch
     * @return string
     */
    protected function createRequestContent(Request $request, int $idBranch): string
    {
        $content = json_decode($request->getContent(), true);
        $content[MerchantProductKeyRequestInterface::KEY_BRANCH_ID] = $idBranch;

        return json_encode($content);
    }
}
