<?php
/**
 * Durst - project - InvoiceReferenceGenerator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 10:47
 */

namespace Pyz\Zed\Invoice\Business\Generator;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface;
use Pyz\Zed\Invoice\InvoiceConfig;

class InvoiceReferenceGenerator implements InvoiceReferenceGeneratorInterface
{
    /**
     * @var \Pyz\Zed\Invoice\InvoiceConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * InvoiceReferenceGenerator constructor.
     *
     * @param \Pyz\Zed\Invoice\InvoiceConfig $config
     * @param \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface $merchantFacade
     * @param \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface $sequenceNumberFacade
     */
    public function __construct(
        InvoiceConfig $config,
        InvoiceToMerchantBridgeInterface $merchantFacade,
        InvoiceToSequenceNumberBridgeInterface $sequenceNumberFacade
    ) {
        $this->config = $config;
        $this->merchantFacade = $merchantFacade;
        $this->sequenceNumberFacade = $sequenceNumberFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string
     */
    public function createInvoiceReference(OrderTransfer $orderTransfer): string
    {
        return $this
            ->createInvoiceReferenceFromBranchId($orderTransfer->getFkBranch());
    }

    /**
     * @param int $idBranch
     *
     * @return string
     */
    public function createInvoiceReferenceFromBranchId(int $idBranch): string
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById($idBranch);

        return $this
            ->sequenceNumberFacade
            ->generate(
                $this->getInvoiceSequenceNumberSettingsTransfer($branch)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getInvoiceSequenceNumberSettingsTransfer(BranchTransfer $branchTransfer) : SequenceNumberSettingsTransfer
    {
        return (new SequenceNumberSettingsTransfer())
            ->setName($this->config->getInvoiceReferenceSequenceName($branchTransfer->getIdBranch()))
            ->setPrefix($this->getPrefix($branchTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return string
     */
    protected function getPrefix(BranchTransfer $branchTransfer): string
    {
        return sprintf(
            '%s%s%d%s',
            $this->config->getReferencePrefix(),
            $this->config->getReferenceSeperator(),
            $branchTransfer->getIdBranch(),
            $this->config->getReferenceSeperator()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return string
     */
    protected function getMerchantIdentifier(BranchTransfer $branchTransfer): string
    {
        return sprintf(
            '%d',
            $branchTransfer->requireFkMerchant()->getFkMerchant()
        );
    }
}
