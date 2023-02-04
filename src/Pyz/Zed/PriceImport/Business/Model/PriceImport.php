<?php
/**
 * Durst - project - PriceImport.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 16:03
 */

namespace Pyz\Zed\PriceImport\Business\Model;


use Generated\Shared\Transfer\PriceImportTransfer;
use Orm\Zed\PriceImport\Persistence\DstPriceImport;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportNoBranchException;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportNoCsvFileException;
use Pyz\Zed\PriceImport\Business\Exception\PriceImportNoRecipientException;
use Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface;

class PriceImport implements PriceImportInterface
{
    /**
     * @var \Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * PriceImport constructor.
     * @param \Pyz\Zed\PriceImport\Persistence\PriceImportQueryContainerInterface $queryContainer
     */
    public function __construct(
        PriceImportQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoBranchException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoCsvFileException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoRecipientException
     */
    public function createPriceImport(PriceImportTransfer $importTransfer): PriceImportTransfer
    {
        $this
            ->assertPriceImport(
                $importTransfer
            );

        $priceImportEntity = $this
            ->findEntityOrCreate(
                $importTransfer
            );

        $priceImportEntity
            ->fromArray(
                $importTransfer
                    ->toArray()
            );

        if (
            $priceImportEntity->isModified() ||
            $priceImportEntity->isNew()
        ) {
            $priceImportEntity
                ->save();
        }

        return $this
            ->entityToTransfer(
                $priceImportEntity
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return void
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoBranchException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoCsvFileException
     * @throws \Pyz\Zed\PriceImport\Business\Exception\PriceImportNoRecipientException
     */
    protected function assertPriceImport(PriceImportTransfer $importTransfer): void
    {
        if ($importTransfer->getCsvFile() === null) {
            throw new PriceImportNoCsvFileException(
                PriceImportNoCsvFileException::MESSAGE
            );
        }

        if ($importTransfer->getFkBranch() === null) {
            throw new PriceImportNoBranchException(
                PriceImportNoBranchException::MESSAGE
            );
        }

        if ($importTransfer->getRecipient() === null) {
            throw new PriceImportNoRecipientException(
                PriceImportNoRecipientException::MESSAGE
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImport
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(PriceImportTransfer $importTransfer): DstPriceImport
    {
        if ($importTransfer->getIdPriceImport() === null) {
            return new DstPriceImport();
        }

        return $this
            ->queryContainer
            ->queryPriceImportById(
                $importTransfer
                    ->getIdPriceImport()
            )
            ->findOneOrCreate();
    }

    /**
     * @param \Orm\Zed\PriceImport\Persistence\DstPriceImport $dstPriceImport
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     */
    protected function entityToTransfer(DstPriceImport $dstPriceImport): PriceImportTransfer
    {
        return (new PriceImportTransfer())
            ->fromArray(
                $dstPriceImport
                    ->toArray(),
                true
            );
    }
}
