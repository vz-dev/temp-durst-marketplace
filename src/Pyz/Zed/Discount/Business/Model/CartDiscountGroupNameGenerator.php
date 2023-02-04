<?php
/**
 * Durst - project - CartDiscountGroupNameGenerator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 09:40
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class CartDiscountGroupNameGenerator implements CartDiscountGroupNameGeneratorInterface
{
    /**
     * @var \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * @var \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    protected $sequenceNumberSettings;

    /**
     * CartDiscountGroupNameGenerator constructor.
     * @param \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param \Generated\Shared\Transfer\SequenceNumberSettingsTransfer $sequenceNumberSettings
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
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return string
     */
    public function generateGroupName(int $idBranch): string
    {
        return $this
            ->sequenceNumberFacade
            ->generate(
                $this
                    ->createSequenceNumberSetting(
                        $idBranch
                    )
            );
    }

    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
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
