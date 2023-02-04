<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class TourReferenceGenerator implements TourReferenceGeneratorInterface
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
     * @param SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
     */
    public function __construct(
        SequenceNumberFacadeInterface $sequenceNumberFacade,
        SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
    ) {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
        $this->sequenceNumberSettings = $sequenceNumberSettingsTransfer;
    }

    /**
     * @return string
     */
    public function generateReference(): string
    {
        return $this
            ->sequenceNumberFacade
            ->generate($this->sequenceNumberSettings);
    }
}

