<?php
/**
 * Durst - project - PasswordNotNullValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 16:07
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class PasswordNotNullValidator extends BaseCustomerValidator implements CustomerValidationInterface
{
    protected const ERROR_CODE = '1000004';
    protected const ERROR_DETAIL = 'The field password is empty.';

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
            $restCustomersAttributesTransfer->getPassword() === null ||
            trim($restCustomersAttributesTransfer->getPassword()) === ''
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
