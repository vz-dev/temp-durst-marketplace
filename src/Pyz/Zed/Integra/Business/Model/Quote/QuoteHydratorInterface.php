<?php
/**
 * Durst - project - QuoteHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:56
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteHydratorInterface
{
    /**
     * @param int $idBranch
     * @param array $orderData
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getQuote(int $idBranch, array $orderData): QuoteTransfer;
}
