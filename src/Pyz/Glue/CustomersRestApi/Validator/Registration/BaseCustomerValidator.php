<?php
/**
 * Durst - project - BaseCustomerValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 14:55
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseCustomerValidator
{
    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     * @param string $code
     * @param string $detail
     * @param string|int $status
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function addErrorToResponse(
        RestResponseInterface $restResponse,
        string $code,
        string $detail,
        string $status = Response::HTTP_BAD_REQUEST
    ): RestResponseInterface
    {
        $restErrorTransfer = (new RestErrorMessageTransfer())
            ->setCode(
                $code
            )
            ->setDetail(
                $detail
            )
            ->setStatus(
                $status
            );

        return $restResponse
            ->addError(
                $restErrorTransfer
            );
    }
}
