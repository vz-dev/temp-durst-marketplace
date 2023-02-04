<?php
/**
 * Durst - project - MerchantBranchBootstrapProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:01
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;

class MerchantBranchBootstrapProvider extends AbstractPlugin implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Silex\Application $app
     */
    public function register(Application $app)
    {
        $config = $this->getFactory()->getConfig();
        $merchantFacade = $this->getFacade();

        $app->before(function (Request $request) use ($app, $merchantFacade, $config) {
            $bundle = $request->attributes->get('module');
            $controller = $request->attributes->get('controller');
            $action = $request->attributes->get('action');

            if ($merchantFacade->isBranchIgnorable($bundle, $controller, $action)) {
                return null;
            }

            if ($merchantFacade->hasCurrentBranch() === true){
                return null;
            }

            return $app->redirect($config->getBranchChooseUrl());
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param \Silex\Application $app
     */
    public function boot(Application $app)
    {
    }
}
