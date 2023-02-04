<?php


namespace Pyz\Yves\AppRestApi\Controller;

use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\Exception\AccessDeniedException;
use Pyz\Yves\AppRestApi\Exception\NoDriverAppReleaseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DriverAppController
 * @package Pyz\Yves\AppRestApi\Controller
 * @method \Pyz\Yves\AppRestApi\AppRestApiFactory getFactory()
 */
class DriverAppController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverLoginRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function branchesAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createDriverBranchRequestHandler()
                    ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function logoutAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverLogoutRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function closeOrderAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverCloseOrderRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function depositAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverDepositRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function gtinAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverGtinRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tourAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                ->getFactory()
                ->createDriverTourRequestHandler()
                ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cancelOrderAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createDriverCancelOrderRequestHandler()
                    ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function latestReleaseAction(Request $request): JsonResponse
    {
        $content = $request
            ->getContent();

        return $this
            ->jsonResponse(
                $this
                    ->getFactory()
                    ->createDriverLatestReleaseRequestHandler()
                    ->handleJson($content)
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadLatestReleaseAction(Request $request): Response
    {
        try {

            $filePath = $this
                ->getFactory()
                ->createDriverDownloadLatestReleaseRequestHandler()
                ->handleJson($request->getContent());

            return new BinaryFileResponse($filePath);
        } catch (AccessDeniedException $exception) {
            return new Response('', 403);
        } catch (NoDriverAppReleaseException $exception){
            return new Response('no release found', 500);
        }
    }
}
