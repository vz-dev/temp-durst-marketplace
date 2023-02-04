<?php
/**
 * Durst - project - IntegraCustomerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.11.20
 * Time: 16:46
 */

namespace Pyz\Zed\Customer\Business\Model;


use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface IntegraCustomerInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string|null
     */
    public function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): ?string;
}
