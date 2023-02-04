<?php
/**
 * Durst - project - FirstnameNotNullValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 15:00
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class FirstnameNotNullValidator extends BaseCustomerValidator implements CustomerValidationInterface
{
    protected const ERROR_CODE = '1000001';
    protected const ERROR_DETAIL = 'The field first name is empty.';

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
            $restCustomersAttributesTransfer->getFirstName() === null ||
            trim($restCustomersAttributesTransfer->getFirstName()) === ''
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
