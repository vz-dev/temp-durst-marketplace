<?php

namespace Pyz\Zed\Tour\Business\Model;

use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;


/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 19.10.18
 * Time: 12:19
 */

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
     * TourReferenceGenerator constructor.
     * @param SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
     */

    public function __construct(
        SequenceNumberFacadeInterface $sequenceNumberFacade,
        SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
    )
    {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
        $this->sequenceNumberSettings = $sequenceNumberSettingsTransfer;
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return string
     */
    public function generateTourReference(ConcreteTourTransfer $concreteTourTransfer) : string
    {
        return $this
            ->sequenceNumberFacade
            ->generate($this->sequenceNumberSettings);

    }

}

