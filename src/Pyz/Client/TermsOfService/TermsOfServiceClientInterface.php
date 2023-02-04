<?php
/**
 * Durst - project - TermsOfServiceClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 11:57
 */

namespace Pyz\Client\TermsOfService;


use Generated\Shared\Transfer\TermsOfServiceTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

interface TermsOfServiceClientInterface
{
    /**
     * Receives the terms of service data set with the name defined in the config and returns a fully hydrated
     * transfer object.
     *
     * @return TermsOfServiceTransfer|TransferInterface
     */
    public function getCustomerTerms() : TermsOfServiceTransfer;
}