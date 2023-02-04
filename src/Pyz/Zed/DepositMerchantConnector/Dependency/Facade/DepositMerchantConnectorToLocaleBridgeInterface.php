<?php
/**
 * Durst - project - DepositMerchantConnectorToLocaleBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 10:50
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\Facade;


interface DepositMerchantConnectorToLocaleBridgeInterface
{
    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getCurrentLocale();
}