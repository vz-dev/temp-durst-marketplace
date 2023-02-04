<?php

namespace Pyz\Yves\AppRestApi\Controller;

use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\AppRestApi\AppRestApiFactory;
use Pyz\Yves\AppRestApi\Exception\InvalidJsonException;
use Pyz\Yves\AppRestApi\Exception\RequestBodyEmptyException;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DepositPickupCreateInquiryKeyResponseInterface;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method AppRestApiFactory getFactory()
 */
class DepositPickupController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createInquiryAction(Request $request)
    {
        $content = $request->getContent();

        try {
            $this->checkContent($content);

            $response = $this
                ->getFactory()
                ->createDepositPickupCreateInquiryRequestHandler()
                ->handleJson($content);
        } catch (RequestBodyEmptyException $exception) {
            return $this
                ->createErrorResponse(
                    RequestBodyEmptyException::STATUS_CODE,
                    RequestBodyEmptyException::ERROR_CODE,
                    RequestBodyEmptyException::MESSAGE
                );
        } catch (InvalidJsonException $exception) {
            return $this
                ->createErrorResponse(
                    InvalidJsonException::STATUS_CODE,
                    InvalidJsonException::ERROR_CODE,
                    InvalidJsonException::MESSAGE
                );
        }

        $statusCode = $this->determineStatusCode($response);

        return $this->jsonResponse($response, $statusCode);
    }

    /**
     * @param string $content
     *
     * @throws RequestBodyEmptyException
     */
    protected function checkContent(string $content): void
    {
        if ($content === null || $content === '') {
            throw new RequestBodyEmptyException();
        }
    }

    /**
     * @param int $statusCode
     * @param string $errorCode
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function createErrorResponse(int $statusCode, string $errorCode, string $message): JsonResponse
    {
        return $this
            ->jsonResponse([
                'error' => [
                    'code' => $errorCode,
                    'message' => $message
                ],
            ], $statusCode);
    }

    /**
     * @param stdClass $response
     *
     * @return int
     */
    protected function determineStatusCode(stdClass $response): int
    {
        if (property_exists($response, DepositPickupCreateInquiryKeyResponseInterface::KEY_ERRORS)) {
            return Response::HTTP_BAD_REQUEST;
        }

        if (property_exists($response, DepositPickupCreateInquiryKeyResponseInterface::KEY_ERROR)) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return Response::HTTP_OK;
    }
}
