<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:03
 */

namespace Pyz\Zed\Merchant\Business\Map;


use Generated\Shared\Search\BranchIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\Merchant\MerchantConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class BranchDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $branchData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $branchData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $paymentProviders = [];

        if (is_string($branchData[BranchIndexMap::PAYMENT_PROVIDER_IDS])) {
            $paymentProviders = array_map('intval' , explode(',', $branchData[BranchIndexMap::PAYMENT_PROVIDER_IDS]));
        }

        $branchData[BranchIndexMap::PAYMENT_PROVIDER_IDS] = $paymentProviders;

        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(MerchantConstants::BRANCH_SEARCH_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::CITY, $branchData[BranchIndexMap::CITY])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::COMPANY_PROFILE, $branchData[BranchIndexMap::COMPANY_PROFILE])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::ID_BRANCH, $branchData[BranchIndexMap::ID_BRANCH])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::NAME,$branchData[BranchIndexMap::NAME])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::PAYMENT_PROVIDER_IDS, $branchData[BranchIndexMap::PAYMENT_PROVIDER_IDS])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::PHONE, $branchData[BranchIndexMap::PHONE])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::STREET, $branchData[BranchIndexMap::STREET])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::TERMS_OF_SERVICE, $branchData[BranchIndexMap::TERMS_OF_SERVICE])
            ->addSearchResultData($pageMapTransfer, BranchIndexMap::ZIP, $branchData[BranchIndexMap::ZIP]);

        return $pageMapTransfer;
    }
}