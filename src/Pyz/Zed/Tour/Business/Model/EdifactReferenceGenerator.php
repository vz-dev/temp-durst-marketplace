<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-07
 * Time: 09:10
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class EdifactReferenceGenerator implements EdifactReferenceGeneratorInterface
{

    protected const EXPORT_TYPE_DATA = 'DATA';
    protected const EXPORT_TYPE_MESSAGE = 'MSG';
    protected const EXPORT_TYPE_DEPOSIT = 'DEPOSIT';

    /**
     * @var SequenceNumberFacadeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * @var SequenceNumberSettingsTransfer
     */
    protected $sequenceNumberSettings;

    /**
     * EdifactReferenceGenerator constructor.
     *
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
     * {@inheritdoc}
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateDataTransferReference(SpyBranch $branch): string
    {
        $idBranch = $branch
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_DATA,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateDataTransferReferenceFromTransfer(BranchTransfer $branchTransfer): string
    {
        $idBranch = $branchTransfer
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_DATA,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritdoc}
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateMessageReference(SpyBranch $branch): string
    {
        $idBranch = $branch
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_MESSAGE,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateMessageReferenceFromTransfer(BranchTransfer $branchTransfer): string
    {
        $idBranch = $branchTransfer
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_MESSAGE,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritdoc}
     *
     * @param SpyBranch $branch
     * @return string
     */
    public function generateDepositReference(SpyBranch $branch): string
    {
        $idBranch = $branch
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_DEPOSIT,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     */
    public function generateDepositReferenceFromTransfer(BranchTransfer $branchTransfer): string
    {
        $idBranch = $branchTransfer
            ->getIdBranch();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                self::EXPORT_TYPE_DEPOSIT,
                $idBranch
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }
}