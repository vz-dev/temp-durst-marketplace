<?php
/**
 * Durst - project - DocumentationGeneratorRestApiDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.11.21
 * Time: 12:55
 */

namespace Pyz\Zed\DocumentationGeneratorRestApi;

use Pyz\Zed\DocumentationGeneratorRestApi\Dependency\External\DocumentationGeneratorRestApiToDoctrineInflectorAdapter;
use Spryker\Glue\GlueApplication\Plugin\DocumentationGeneratorRestApi\ResourceRelationshipCollectionProviderPlugin;
use Spryker\Glue\GlueApplication\Plugin\DocumentationGeneratorRestApi\ResourceRoutePluginsProviderPlugin;
use Spryker\Zed\DocumentationGeneratorRestApi\DocumentationGeneratorRestApiDependencyProvider as SprykerDocumentationGeneratorRestApiDependencyProvider;
use Spryker\Zed\Kernel\Container;

class DocumentationGeneratorRestApiDependencyProvider extends SprykerDocumentationGeneratorRestApiDependencyProvider
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getResourceRoutePluginProviderPlugins(): array
    {
        $plugins = parent::getResourceRoutePluginProviderPlugins();

        $plugins[] = new ResourceRoutePluginsProviderPlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getResourceRelationshipCollectionProviderPlugins(): array
    {
        $plugins = parent::getResourceRelationshipCollectionProviderPlugins();

        $plugins[] = new ResourceRelationshipCollectionProviderPlugin();

        return $plugins;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTextInflector(Container $container): Container
    {
        $container[static::TEXT_INFLECTOR] = function () {
            return new DocumentationGeneratorRestApiToDoctrineInflectorAdapter();
        };

        return $container;
    }
}
