<?php


namespace Pyz\Zed\Auth\Business\Model;


use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

class JwtNumberGenerator implements JwtNumberGeneratorInterface
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
     * JwtNumberGenerator constructor.
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
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return string
     */
    public function generateDriverTokenNumber(DriverTransfer $driverTransfer): string
    {
        $idDriver = $driverTransfer
            ->getIdDriver();

        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(sprintf(
                $this->sequenceNumberSettings->getName(),
                $idDriver
            ));

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }
}