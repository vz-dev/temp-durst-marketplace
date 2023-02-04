<?php
/**
 * Durst - project - BranchTransferResultFormatter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.11.18
 * Time: 11:54
 */

namespace Pyz\Client\Merchant\Plugin\ResultFormatter;

use ArrayObject;
use Elastica\Result;
use Elastica\ResultSet;
use Generated\Shared\Search\BranchIndexMap;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

class BranchTransferResultFormatter extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'branch_transfer_result_formatter';

    /**
     * @param \Elastica\ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return mixed
     */
    protected function formatSearchResult(
        ResultSet $searchResult,
        array $requestParameters
    ) {
        $transfers = [];
        foreach ($searchResult as $result) {
            $transfers[] = $this->resultToTransfer($result);
        }

        return $transfers;
    }

    /**
     * @param \Elastica\Result $result
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    protected function resultToTransfer(Result $result): BranchTransfer
    {
        $source = $result->getSource();

        return (new BranchTransfer())
            ->setIdBranch($source[BranchIndexMap::ID_BRANCH])
            ->setCity($source[BranchIndexMap::CITY])
            ->setCompanyProfile($source[BranchIndexMap::COMPANY_PROFILE])
            ->setName($source[BranchIndexMap::NAME])
            ->setPhone($source[BranchIndexMap::PHONE])
            ->setStreet($source[BranchIndexMap::STREET])
            ->setTermsOfService($source[BranchIndexMap::TERMS_OF_SERVICE])
            ->setZip($source[BranchIndexMap::ZIP])
            ->setPaymentMethods(
                $this->paymentMethodToTransfer(
                    $source[BranchIndexMap::PAYMENT_PROVIDER][$this->getChildField(BranchIndexMap::PAYMENT_PROVIDER_PAYMENT_METHODS, 1)]
                )
            );
    }

    /**
     * @param array $paymentMethods
     *
     * @return \ArrayObject
     */
    protected function paymentMethodToTransfer(array $paymentMethods): ArrayObject
    {
        $paymentMethodTransfers = [];
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodTransfers[] = (new PaymentMethodTransfer())
                ->setName($paymentMethod[$this->getChildField(BranchIndexMap::PAYMENT_PROVIDER_PAYMENT_METHODS_NAME, 2)])
                ->setCode($paymentMethod[$this->getChildField(BranchIndexMap::PAYMENT_PROVIDER_PAYMENT_METHODS_KEY, 2)])
                ->setMethodName($paymentMethod[$this->getChildField(BranchIndexMap::PAYMENT_PROVIDER_PAYMENT_METHODS_KEY, 2)]);
        }

        return new ArrayObject($paymentMethodTransfers);
    }

    /**
     * @param string $field
     * @param int $level
     *
     * @return string
     */
    protected function getChildField(string $field, int $level): string
    {
        return explode('.', $field)[$level];
    }

    /**
     * @api
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
