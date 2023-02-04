<?php
/**
 * Durst - project - LatLngOrderAddressSaverInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-09
 * Time: 08:16
 */

namespace Pyz\Zed\GoogleApi\Business\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface LatLngOrderAddressSaverInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function saveLatLngToAddress(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);
}
