<?php
/**
 * Durst - project - ProductExport.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:37
 */

namespace Pyz\Zed\ProductExport\Business\Model;


use Generated\Shared\Transfer\ProductExportTransfer;
use Orm\Zed\ProductExport\Persistence\DstProductExport;
use Pyz\Zed\ProductExport\Business\Exception\ProductExportNoBranchException;
use Pyz\Zed\ProductExport\Business\Exception\ProductExportNoMailException;
use Pyz\Zed\ProductExport\Business\Exception\ProductExportNotFoundException;
use Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface;

class ProductExport implements ProductExportInterface
{
    /**
     * @var \Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * ProductExport constructor.
     * @param \Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface $queryContainer
     */
    public function __construct(
        ProductExportQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\ProductExportTransfer $productExportTransfer
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     * @throws \Pyz\Zed\ProductExport\Business\Exception\ProductExportNoMailException
     * @throws \Pyz\Zed\ProductExport\Business\Exception\ProductExportNoBranchException
     */
    public function createProductExport(ProductExportTransfer $productExportTransfer): ProductExportTransfer
    {
        if ($productExportTransfer->getRecipient() === null) {
            throw new ProductExportNoMailException(
                ProductExportNoMailException::MESSAGE
            );
        }

        if ($productExportTransfer->getFkBranch() === null) {
            throw new ProductExportNoBranchException(
                ProductExportNoBranchException::MESSAGE
            );
        }

        $productExportEntity = $this
            ->findEntityOrCreate(
                $productExportTransfer
            );

        $productExportEntity
            ->fromArray(
                $productExportTransfer
                    ->toArray()
            );

        if (
            $productExportEntity->isNew() ||
            $productExportEntity->isModified()
        ) {
            $productExportEntity
                ->save();
        }

        return $this
            ->entityToTransfer(
                $productExportEntity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idProductExport
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     * @throws \Pyz\Zed\ProductExport\Business\Exception\ProductExportNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getProductExportById(int $idProductExport): ProductExportTransfer
    {
        $export = $this
            ->queryContainer
            ->queryProductExport()
            ->filterByIdProductExport(
                $idProductExport
            )
            ->findOne();

        if ($export->getIdProductExport() === null) {
            throw new ProductExportNotFoundException(
                sprintf(
                    ProductExportNotFoundException::MESSAGE,
                    $idProductExport
                )
            );
        }

        return $this
            ->entityToTransfer(
                $export
            );
    }

    /**
     * @param \Orm\Zed\ProductExport\Persistence\DstProductExport $productExport
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     */
    protected function entityToTransfer(DstProductExport $productExport): ProductExportTransfer
    {
        $export = new ProductExportTransfer();

        $export
            ->fromArray(
                $productExport
                    ->toArray(),
                true
            );

        return $export;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductExportTransfer $transfer
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExport
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(ProductExportTransfer $transfer): DstProductExport
    {
        if ($transfer->getIdProductExport() === null) {
            return new DstProductExport();
        }

        return $this
            ->queryContainer
            ->queryProductExportById(
                $transfer
                    ->getIdProductExport()
            )
            ->findOneOrCreate();
    }
}
