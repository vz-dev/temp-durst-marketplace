<?php
/**
 * Durst - project - CustomerValidationInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 14:23
 */

namespace Pyz\Glue\CustomersRestApi\Validator\Registration;

use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface CustomerValidationInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCustomersAttributesTransfer $restCustomersAttributesTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function isValid(
        RestCustomersAttributesTransfer $restCustomersAttributesTransfer,
        RestResponseInterface $restResponse
    ): RestResponseInterface;
}
