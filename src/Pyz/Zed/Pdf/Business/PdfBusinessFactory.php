<?php
/**
 * Durst - project - PdfBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 11:17
 */

namespace Pyz\Zed\Pdf\Business;


use Pyz\Zed\Pdf\Business\Model\PdfFile;
use Pyz\Zed\Pdf\Business\Model\PdfFileInterface;
use Pyz\Zed\Pdf\Business\Model\PdfManager;
use Pyz\Zed\Pdf\Business\Model\PdfManagerInterface;
use Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridgeInterface;
use Pyz\Zed\Pdf\PdfConfig;
use Pyz\Zed\Pdf\PdfDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class PdfBusinessFactory
 * @package Pyz\Zed\Pdf\Business
 * @method PdfConfig getConfig()
 */
class PdfBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Pdf\Business\Model\PdfFileInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPdfFileModel(): PdfFileInterface
    {
        return new PdfFile(
            $this->createPdfManager(),
            $this->getPdfRenderer(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Pdf\Business\Model\PdfManagerInterface
     */
    public function createPdfManager(): PdfManagerInterface
    {
        return new PdfManager();
    }

    /**
     * @return \Pyz\Zed\Pdf\Dependency\Renderer\PdfToRendererBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPdfRenderer(): PdfToRendererBridgeInterface
    {
        return $this
            ->getProvidedDependency(PdfDependencyProvider::RENDERER);
    }
}
