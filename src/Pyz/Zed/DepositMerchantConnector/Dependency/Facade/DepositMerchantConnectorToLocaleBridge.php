<?php
/**
 * Durst - project - DepositMerchantConnectorToLocaleBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 10:50
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\Facade;


class DepositMerchantConnectorToLocaleBridge implements DepositMerchantConnectorToLocaleBridgeInterface
{
    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * DepositMerchantConnectorToLocaleBridge constructor.
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     */
    public function __construct(\Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade)
    {
        $this->localeFacade = $localeFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getCurrentLocale()
    {
        return $this
            ->localeFacade
            ->getCurrentLocale();
    }
}