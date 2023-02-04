<?php
/**
 * Durst - project - ProductExportFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:45
 */

namespace Pyz\Zed\ProductExport\Business;


use Generated\Shared\Transfer\ProductExportTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class ProductExportFacade
 * @package Pyz\Zed\ProductExport\Business
 * @method ProductExportBusinessFactory getFactory()
 */
class ProductExportFacade extends AbstractFacade implements ProductExportFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\ProductExportTransfer $productExportTransfer
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductExport(ProductExportTransfer $productExportTransfer): ProductExportTransfer
    {
        return $this
            ->getFactory()
            ->createProductExportModel()
            ->createProductExport(
                $productExportTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function exportNext(): void
    {
        $this
            ->getFactory()
            ->createProductExportManager()
            ->exportNext();
    }
}
