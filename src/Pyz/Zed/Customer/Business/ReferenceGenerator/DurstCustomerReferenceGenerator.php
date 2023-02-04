<?php

namespace Pyz\Zed\Customer\Business\ReferenceGenerator;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Zed\Customer\CustomerConfig;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGenerator as SprykerCustomerReferenceGenerator;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToSequenceNumberInterface;

class DurstCustomerReferenceGenerator extends SprykerCustomerReferenceGenerator implements DurstCustomerReferenceGeneratorInterface
{
    /**
     * @var CustomerConfig
     */
    protected $config;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @param CustomerToSequenceNumberInterface $sequenceNumberFacade
     * @param SequenceNumberSettingsTransfer $sequenceNumberSettings
     * @param CustomerConfig $config
     * @param SalesFacadeInterface $salesFacade
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        CustomerToSequenceNumberInterface $sequenceNumberFacade,
        SequenceNumberSettingsTransfer $sequenceNumberSettings,
        CustomerConfig $config,
        SalesFacadeInterface $salesFacade,
        MerchantFacadeInterface $merchantFacade
    ) {
        parent::__construct($sequenceNumberFacade, $sequenceNumberSettings);

        $this->config = $config;
        $this->salesFacade = $salesFacade;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param int $idBranch
     * @param CustomerTransfer $customerTransfer
     *
     * @return string
     */
    public function generateDurstCustomerReferenceForMerchant(int $idBranch, CustomerTransfer $customerTransfer): string
    {
        $idMerchant = $this
            ->merchantFacade
            ->getMerchantByIdBranch($idBranch)
            ->getIdMerchant();

        $customerReference = $this->findExistingDurstCustomerReference($idMerchant, $customerTransfer);

        if ($customerReference !== null) {
            return $customerReference;
        }

        return $this
            ->facadeSequenceNumber
            ->generate(
                $this->config->getDurstCustomerReferenceSequenceNumberSettings($idMerchant)
            );
    }

    /**
     * @param int $idMerchant
     * @param CustomerTransfer $customerTransfer
     *
     * @return string|null
     */
    protected function findExistingDurstCustomerReference(int $idMerchant, CustomerTransfer $customerTransfer): ?string
    {
        $orderTransfer = $this
            ->salesFacade
            ->findOrderWithDurstCustomerReferenceByIdMerchantAndEmail(
                $idMerchant,
                $customerTransfer->getEmail()
            );

        if ($orderTransfer === null || $orderTransfer->getDurstCustomerReference() === null) {
            return null;
        }

        return $orderTransfer->getDurstCustomerReference();
    }
}
