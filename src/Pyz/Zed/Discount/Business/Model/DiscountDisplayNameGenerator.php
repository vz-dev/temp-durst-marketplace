<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-07
 * Time: 16:22
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class DiscountDisplayNameGenerator implements DiscountDisplayNameGeneratorInterface
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
     * DiscountDisplayNameGenerator constructor.
     * @param SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param SequenceNumberSettingsTransfer $sequenceNumberSettings
     */
    public function __construct(SequenceNumberFacadeInterface $sequenceNumberFacade, SequenceNumberSettingsTransfer $sequenceNumberSettings)
    {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
        $this->sequenceNumberSettings = $sequenceNumberSettings;
    }

    /**
     * @param int $idBranch
     * @return string
     */
    public function generateDisplayName(int $idBranch): string
    {
        return $this
            ->sequenceNumberFacade
            ->generate($this->createSequenceNumberSetting($idBranch));
    }

    /**
     * @param int $idBranch
     * @return SequenceNumberSettingsTransfer
     */
    protected function createSequenceNumberSetting(int $idBranch): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceName = sprintf(
            $this->sequenceNumberSettings->getName(),
            $idBranch
        );

        $sequencePrefix = sprintf(
            $this->sequenceNumberSettings->getPrefix(),
            $idBranch
        );

        $sequenceNumberSettings
            ->setName($sequenceName)
            ->setPrefix($sequencePrefix);

        return $sequenceNumberSettings;
    }
}