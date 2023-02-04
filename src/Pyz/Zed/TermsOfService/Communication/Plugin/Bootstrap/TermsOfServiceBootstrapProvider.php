<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 14:00
 */

namespace Pyz\Zed\TermsOfService\Communication\Plugin\Bootstrap;


use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Pyz\Zed\TermsOfService\Communication\TermsOfServiceCommunicationFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TermsOfServiceBootstrapProvider
 * @package Pyz\Zed\TermsOfService\Communication\Plugin\Bootstrap
 * @method TermsOfServiceFacadeInterface getFacade()
 * @method TermsOfServiceCommunicationFactory getFactory()
 */
class TermsOfServiceBootstrapProvider extends AbstractPlugin implements ServiceProviderInterface
{
    public function register(Application $app)
    {
    }

    /**
     * @param Application $app
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function boot(Application $app)
    {
        $config = $this->getFactory()->getConfig();
        $termsOfServiceFacade = $this->getFacade();
        $merchantFacade = $this
            ->getFactory()
            ->getMerchantFacade();

        $app->before(function (Request $request) use ($app, $termsOfServiceFacade, $config, $merchantFacade) {
            $bundle = $request->attributes->get('module');
            $controller = $request->attributes->get('controller');
            $action = $request->attributes->get('action');

            if ($termsOfServiceFacade->isRouteIgnorable($bundle, $controller, $action) === true) {
                return null;
            }

            $idCurrentMerchant = $merchantFacade
                ->getCurrentMerchant()
                ->getIdMerchant();

            if($termsOfServiceFacade->hasUnacceptedTermsOfServiceByIdMerchant($idCurrentMerchant) !== true){
                return null;
            }

            return $app->redirect($config->getTermsOfServiceFormUrl());
        });
    }
}