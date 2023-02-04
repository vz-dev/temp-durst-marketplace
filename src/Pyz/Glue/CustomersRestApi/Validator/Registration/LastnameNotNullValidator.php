<?php
/**
 * Durst - project - LastnameNotNullValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 15:55
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class LastnameNotNullValidator extends BaseCustomerValidator implements CustomerValidationInterface
{
    protected const ERROR_CODE = '1000002';
    protected const ERROR_DETAIL = 'The field last name is empty.';

    /**
     * @inheritDoc
     */
    public function isValid(
        RestCustomersAttributesTransfer $restCustomersAttributesTransfer,
        RestResponseInterface $restResponse
    ): RestResponseInterface
    {
        if (
            $restCustomersAttributesTransfer->getLastName() === null ||
            trim($restCustomersAttributesTransfer->getLastName()) === ''
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
