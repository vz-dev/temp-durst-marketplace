<?php
/**
 * Durst - project - GlueApplicationDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.11.21
 * Time: 10:25
 */

namespace Pyz\Glue\GlueApplication;

use Pyz\Glue\OrdersRestApi\Plugin\OrdersResourceRoutePlugin;
use Spryker\Glue\AuthRestApi\Plugin\AccessTokensResourceRoutePlugin;
use Spryker\Glue\AuthRestApi\Plugin\AccessTokenValidatorPlugin;
use Spryker\Glue\AuthRestApi\Plugin\FormatAuthenticationErrorResponseHeadersPlugin;
use Spryker\Glue\AuthRestApi\Plugin\RefreshTokensResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\CustomersRestApiConfig;
use Spryker\Glue\CustomersRestApi\Plugin\AddressesResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\Plugin\CustomerForgottenPasswordResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\Plugin\CustomerPasswordResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\Plugin\CustomerRestorePasswordResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\Plugin\CustomersResourceRoutePlugin;
use Spryker\Glue\CustomersRestApi\Plugin\CustomersToAddressesRelationshipPlugin;
use Spryker\Glue\CustomersRestApi\Plugin\SetCustomerBeforeActionPlugin;
use Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider as SprykerGlueApplicationDependencyProvider;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface;

class GlueApplicationDependencyProvider extends SprykerGlueApplicationDependencyProvider
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getResourceRoutePlugins(): array
    {
        $plugins = parent::getResourceRoutePlugins();

        // Token endpoint
        $plugins[] = new AccessTokensResourceRoutePlugin();
        $plugins[] = new RefreshTokensResourceRoutePlugin();
        // Customer endpoint
        $plugins[] = new CustomersResourceRoutePlugin();
        $plugins[] = new CustomerForgottenPasswordResourceRoutePlugin();
        $plugins[] = new CustomerRestorePasswordResourceRoutePlugin();
        $plugins[] = new CustomerPasswordResourceRoutePlugin();
        // Address endpoint
        $plugins[] = new AddressesResourceRoutePlugin();
        // Order endpoint
        $plugins[] = new OrdersResourceRoutePlugin();
        // Wishlist endpoint
//        $plugins[] = new WishlistsResourceRoutePlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface $resourceRelationshipCollection
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface
     */
    protected function getResourceRelationshipPlugins(
        ResourceRelationshipCollectionInterface $resourceRelationshipCollection
    ): ResourceRelationshipCollectionInterface
    {
        // Relationship customer <-> address
        $resourceRelationshipCollection
            ->addRelationship(
                CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                new CustomersToAddressesRelationshipPlugin()
            );

        // Relationship customer <-> wishlist
//        $resourceRelationshipCollection
//            ->addRelationship(
//                CustomersRestApiConfig::RESOURCE_CUSTOMERS,
//                new WishlistRelationshipByResourceIdPlugin()
//            );

        return $resourceRelationshipCollection;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getControllerBeforeActionPlugins(): array
    {
        $plugins = parent::getControllerBeforeActionPlugins();

        $plugins[] = new SetCustomerBeforeActionPlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getValidateRestRequestPlugins(): array
    {
        $plugins = parent::getValidateRestRequestPlugins();

        $plugins[] = new AccessTokenValidatorPlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getFormatResponseHeadersPlugins(): array
    {
        $plugins = parent::getFormatResponseHeadersPlugins();

        $plugins[] = new FormatAuthenticationErrorResponseHeadersPlugin();

        return $plugins;
    }
}
