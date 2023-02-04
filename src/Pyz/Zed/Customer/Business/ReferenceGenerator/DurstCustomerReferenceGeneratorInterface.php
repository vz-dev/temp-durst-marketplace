<?php

namespace Pyz\Zed\Customer\Business\ReferenceGenerator;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGeneratorInterface as SprykerCustomerReferenceGeneratorInterface;

interface DurstCustomerReferenceGeneratorInterface extends SprykerCustomerReferenceGeneratorInterface
{
    /**
     * @param int $idBranch
     * @param CustomerTransfer $customerTransfer
     *
     * @return string
     */
    public function generateDurstCustomerReferenceForMerchant(int $idBranch, CustomerTransfer $customerTransfer): string;
}
