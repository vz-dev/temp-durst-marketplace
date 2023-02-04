<?php
/**
 * Durst - project - LicenseInvoiceReferenceGenerator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.20
 * Time: 14:35
 */

namespace Pyz\Zed\Accounting\Business\Model;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;
use Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface;

class LicenseInvoiceReferenceGenerator implements LicenseInvoiceReferenceGeneratorInterface
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
     * @var \Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface
     */
    protected $sequenceNumberQueryContainer;

    /**
     * LicenseInvoiceReferenceGenerator constructor.
     * @param \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface $sequenceNumberFacade
     * @param \Generated\Shared\Transfer\SequenceNumberSettingsTransfer $sequenceNumberSettings
     * @param \Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface $sequenceNumberQueryContainer
     */
    public function __construct(
        SequenceNumberFacadeInterface $sequenceNumberFacade,
        SequenceNumberSettingsTransfer $sequenceNumberSettings,
        SequenceNumberQueryContainerInterface $sequenceNumberQueryContainer
    )
    {
        $this->sequenceNumberFacade = $sequenceNumberFacade;
        $this->sequenceNumberSettings = $sequenceNumberSettings;
        $this->sequenceNumberQueryContainer = $sequenceNumberQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return string
     */
    public function generateLicenseInvoiceNumber(int $idMerchant): string
    {
        $sequenceNumberSettings = $this
            ->setSequenceNumberSettingsName($idMerchant);

        return $this
            ->sequenceNumberFacade
            ->generate($sequenceNumberSettings);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return string
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getLicenseInvoiceNumberByIdMerchant(int $idMerchant): string
    {
        $sequenceNumberSettings = $this
            ->setSequenceNumberSettingsName($idMerchant);

        /* @var $query \Orm\Zed\SequenceNumber\Persistence\SpySequenceNumberQuery */
        $query = $this
            ->sequenceNumberQueryContainer
            ->querySequenceNumber();

        $sequenceNumberEntity = $query
            ->filterByName(
                $sequenceNumberSettings
                    ->getName()
            )
            ->findOne();

        return $sequenceNumberEntity
            ->getCurrentId();
    }

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    protected function setSequenceNumberSettingsName(int $idMerchant): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettings = clone $this->sequenceNumberSettings;

        $sequenceNumberSettings
            ->setName(
                sprintf(
                    $this->sequenceNumberSettings->getName(),
                    $idMerchant
                )
            );

        return $sequenceNumberSettings;
    }
}
