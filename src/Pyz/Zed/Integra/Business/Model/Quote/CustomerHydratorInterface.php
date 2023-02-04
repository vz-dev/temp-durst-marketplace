<?php
/**
 * Durst - project - CustomerHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 10:02
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerHydratorInterface
{
    /**
     * @param array $orderData
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function createCustomerTransfer(array $orderData): CustomerTransfer;
}
