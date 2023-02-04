<?php
/**
 * Durst - project - OauthDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.11.21
 * Time: 10:55
 */

namespace Pyz\Zed\Oauth;

use Spryker\Zed\Oauth\OauthDependencyProvider as SprykerOauthDependencyProvider;
use Spryker\Zed\OauthCustomerConnector\Communication\Plugin\Oauth\CustomerOauthScopeProviderPlugin;
use Spryker\Zed\OauthCustomerConnector\Communication\Plugin\Oauth\CustomerOauthUserProviderPlugin;

class OauthDependencyProvider extends SprykerOauthDependencyProvider
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getUserProviderPlugins(): array
    {
        $plugins = parent::getUserProviderPlugins();

        $plugins[] = new CustomerOauthUserProviderPlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getScopeProviderPlugins(): array
    {
        $plugins = parent::getScopeProviderPlugins();

        $plugins[] = new CustomerOauthScopeProviderPlugin();

        return $plugins;
    }
}
