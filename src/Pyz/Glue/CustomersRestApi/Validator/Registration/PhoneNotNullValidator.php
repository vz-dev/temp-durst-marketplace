<?php
/**
 * Durst - project - PhoneNotNullValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 15:59
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class PhoneNotNullValidator extends BaseCustomerValidator implements CustomerValidationInterface
{
    protected const ERROR_CODE = '1000003';
    protected const ERROR_DETAIL = 'The field phone is empty.';

    /**
     * @inheritDoc
     */
    public function isValid(
        RestCustomersAttributesTransfer $restCustomersAttributesTransfer,
        RestResponseInterface $restResponse
    ): RestResponseInterface
    {
        if (
            $restCustomersAttributesTransfer->getPhone() === null ||
            trim($restCustomersAttributesTransfer->getPhone()) === ''
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
