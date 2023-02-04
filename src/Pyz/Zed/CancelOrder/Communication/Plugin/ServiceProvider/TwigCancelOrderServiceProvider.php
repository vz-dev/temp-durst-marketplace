<?php
/**
 * Durst - project - TwigCancelOrderServiceProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 09:41
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\ServiceProvider;

use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Communication\Plugin\TwigCancelTokenPlugin;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class TwigCancelOrderServiceProvider
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\ServiceProvider
 *
 * @method CancelOrderFacadeInterface getFacade()
 * @method CancelOrderConfig getConfig()
 */
class TwigCancelOrderServiceProvider extends AbstractPlugin implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Silex\Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['twig'] = $app
            ->share(
                $app
                    ->extend(
                        'twig',
                        function (\Twig_Environment $environment) {
                            $environment
                                ->addFunction(
                                    $this
                                        ->getCancelOrderFunction(
                                            $environment
                                        )
                                );

                            return $environment;
                        }
                    )
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Silex\Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
    }

    /**
     * @param \Twig_Environment $environment
     * @return \Pyz\Zed\CancelOrder\Communication\Plugin\TwigCancelTokenPlugin
     */
    protected function getCancelOrderFunction(
        \Twig_Environment $environment
    ): TwigCancelTokenPlugin
    {
        return new TwigCancelTokenPlugin(
            $this
                ->getFacade(),
            $environment,
            $this
                ->getConfig()
        );
    }
}
