<?php
/**
 * Durst - project - ConfirmedPasswordEqualsPasswordValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 16:09
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class ConfirmedPasswordEqualsPasswordValidator extends BaseCustomerValidator implements CustomerValidationInterface
{
    protected const ERROR_CODE = '1000005';
    protected const ERROR_DETAIL = 'Password and confirmed password do not match.';

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\RestCustomersAttributesTransfer $restCustomersAttributesTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function isValid(
        RestCustomersAttributesTransfer $restCustomersAttributesTransfer,
        RestResponseInterface $restResponse
    ): RestResponseInterface
    {
        if (
            $restCustomersAttributesTransfer->getPassword() !== $restCustomersAttributesTransfer->getConfirmPassword()
        ) {
            return $this
                ->addErrorToResponse(
                    $restResponse,
                    static::ERROR_CODE,
                    static::ERROR_DETAIL
                );
        }

        return $restResponse;
    }
}
