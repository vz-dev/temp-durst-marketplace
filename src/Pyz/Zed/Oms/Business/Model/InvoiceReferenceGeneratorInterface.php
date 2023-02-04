<?php
/**
 * Durst - merchant_center - InvoiceReferenceGeneratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.08.18
 * Time: 11:01
 */

namespace Pyz\Zed\Oms\Business\Model;


interface InvoiceReferenceGeneratorInterface
{
    /**
     * @return string
     */
    public function generateInvoiceReference() : string;
}