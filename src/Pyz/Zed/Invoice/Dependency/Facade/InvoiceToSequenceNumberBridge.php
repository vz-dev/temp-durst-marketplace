<?php
/**
 * Durst - project - InvoiceToSequenceNumberFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:06
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class InvoiceToSequenceNumberBridge implements InvoiceToSequenceNumberBridgeInterface
{
    /**
     * @var \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * InvoiceToSequenceNumberFacade constructor.
     *
     * @param \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface $sequenceNumberFacade
     */
    public function __construct(SequenceNumberFacadeInterface $sequenceNumberFacade)
    {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
     *
     * @return string
     */
    public function generate(SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer): string
    {
        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettingsTransfer);
    }
}
