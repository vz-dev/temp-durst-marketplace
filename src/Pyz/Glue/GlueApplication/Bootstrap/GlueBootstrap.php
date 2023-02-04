<?php
/**
 * Durst - project - GlueBootstrap.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.11.21
 * Time: 10:22
 */

namespace Pyz\Glue\GlueApplication\Bootstrap;

use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Spryker\Glue\GlueApplication\Bootstrap\AbstractGlueBootstrap;
use Spryker\Glue\GlueApplication\Plugin\Rest\GlueServiceProviderPlugin;
use Spryker\Glue\GlueApplication\Plugin\Rest\ServiceProvider\GlueApplicationServiceProvider;
use Spryker\Glue\GlueApplication\Plugin\Rest\ServiceProvider\GlueResourceBuilderService;
use Spryker\Glue\GlueApplication\Plugin\Rest\ServiceProvider\GlueRoutingServiceProvider;

class GlueBootstrap extends AbstractGlueBootstrap
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function registerServiceProviders(): void
    {
        $this
            ->application
            ->register(new GlueResourceBuilderService())
            ->register(new GlueApplicationServiceProvider())
            ->register(new SessionServiceProvider())
            ->register(new ServiceControllerServiceProvider())
            ->register(new GlueServiceProviderPlugin())
            ->register(new GlueRoutingServiceProvider());
    }
}
