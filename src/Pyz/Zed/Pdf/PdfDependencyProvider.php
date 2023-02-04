<?php
/**
 * Durst - project - PdfDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 10:46
 */

namespace Pyz\Zed\Pdf;

use Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridge;
use Spryker\Zed\Glossary\Communication\Plugin\TwigTranslatorPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Communication\Plugin\Pimple;
use Spryker\Zed\Kernel\Container;
use Twig_Environment;

class PdfDependencyProvider extends AbstractBundleDependencyProvider
{
    public const RENDERER = 'twig';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = $this->addRenderer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRenderer(Container $container): Container
    {
        $container[static::RENDERER] = function() {
            $twig = $this
                ->getTwigEnvironment();

            if ($twig->hasExtension(TwigTranslatorPlugin::class) !== true) {
                $translator = new TwigTranslatorPlugin();
                $twig
                    ->addExtension($translator);
            }

            return new PdfToRendererBridge(
                $twig
            );
        };

        return $container;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment(): Twig_Environment
    {
        $pimplePlugin = new Pimple();

        $twig = $pimplePlugin
            ->getApplication()['twig'];

        return $twig;
    }
}
