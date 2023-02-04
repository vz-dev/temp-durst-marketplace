<?php
/**
 * Durst - project - PriceImportFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 10:56
 */

namespace Pyz\Zed\PriceImport\Business;


use Generated\Shared\Transfer\PriceImportTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class PriceImportFacade
 * @package Pyz\Zed\PriceImport\Business
 * @method PriceImportBusinessFactory getFactory()
 */
class PriceImportFacade extends AbstractFacade implements PriceImportFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPriceImport(PriceImportTransfer $importTransfer): PriceImportTransfer
    {
        return $this
            ->getFactory()
            ->createPriceImportModel()
            ->createPriceImport(
                $importTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function importNext(): array
    {
        return $this
            ->getFactory()
            ->createPriceImportManager()
            ->importNext();
    }

}
