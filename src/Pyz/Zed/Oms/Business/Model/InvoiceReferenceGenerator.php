<?php
/**
 * Durst - merchant_center - InvoiceReferenceGenerator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.08.18
 * Time: 11:02
 */

namespace Pyz\Zed\Oms\Business\Model;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class InvoiceReferenceGenerator implements InvoiceReferenceGeneratorInterface
{
    /**
     * @var SequenceNumberFacadeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * @var SequenceNumberSettingsTransfer
     */
    protected $sequenceNumberSettings;

    /**
     * InvoiceReferenceGenerator constructor.
     * @param SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param SequenceNumberSettingsTransfer $sequenceNumberSettings
     */
    public function __construct(
        SequenceNumberFacadeInterface $sequenceNumberFacade,
        SequenceNumberSettingsTransfer $sequenceNumberSettings
    )
    {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
        $this->sequenceNumberSettings = $sequenceNumberSettings;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function generateInvoiceReference(): string
    {
        return $this
            ->sequenceNumberFacade
            ->generate($this->sequenceNumberSettings);
    }
}