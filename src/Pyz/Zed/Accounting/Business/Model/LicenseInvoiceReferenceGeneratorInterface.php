<?php
/**
 * Durst - project - LicenseInvoiceReferenceGeneratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.20
 * Time: 14:35
 */

namespace Pyz\Zed\Accounting\Business\Model;

interface LicenseInvoiceReferenceGeneratorInterface
{
    /**
     * @param int $idMerchant
     * @return string
     */
    public function generateLicenseInvoiceNumber(int $idMerchant): string;

    /**
     * @param int $idMerchant
     * @return string
     */
    public function getLicenseInvoiceNumberByIdMerchant(int $idMerchant): string;
}
