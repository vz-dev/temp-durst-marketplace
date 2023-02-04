<?php
/**
 * Durst - project - PdfToRendererBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 10:29
 */

namespace Pyz\Zed\Pdf\Dependency\Renderer;


use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Shared\Kernel\Store;
use Twig_Environment;

class PdfToRendererBridge implements PdfToRendererBridgeInterface
{
    protected const EXTENSION_TRANSLATOR = 'translator';

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * PdfToRendererBridge constructor.
     * @param \Twig_Environment $twigEnvironment
     */
    public function __construct(
        Twig_Environment $twigEnvironment
    )
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $template
     * @param array $options
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Spryker\Shared\Kernel\Locale\LocaleNotFoundException
     */
    public function render(string $template, array $options): string
    {
        $this
            ->setupTranslation();

        return $this
            ->twigEnvironment
            ->render(
                $template,
                $options
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @return void
     * @throws \Twig_Error_Runtime
     */
    public function setLocaleTransfer(LocaleTransfer $localeTransfer): void
    {
        $translator = $this
            ->getTranslator();

        $translator
            ->setLocaleTransfer($localeTransfer);
    }

    /**
     * @throws \Spryker\Shared\Kernel\Locale\LocaleNotFoundException
     * @throws \Twig_Error_Runtime
     */
    protected function setupTranslation()
    {
        $localeTransfer = (new LocaleTransfer())
            ->setLocaleName(
                Store::getInstance()
                    ->getCurrentLocale()
            );

        $this
            ->setLocaleTransfer($localeTransfer);
    }

    /**
     * @return \Twig_ExtensionInterface|\Spryker\Zed\Glossary\Communication\Plugin\TwigTranslatorPlugin
     * @throws \Twig_Error_Runtime
     */
    protected function getTranslator()
    {
        return $this
            ->twigEnvironment
            ->getExtension(
                static::EXTENSION_TRANSLATOR
            );
    }
}
