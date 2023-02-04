<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:21
 */

namespace Pyz\Zed\Merchant\Business\Map;


use Generated\Shared\Search\PaymentProviderIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\Merchant\MerchantConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class PaymentProviderDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $paymentProviderData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $paymentProviderData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(MerchantConstants::PAYMENT_PROVIDER_SEARCH_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, PaymentProviderIndexMap::CODE, $paymentProviderData[PaymentProviderIndexMap::CODE])
            ->addSearchResultData($pageMapTransfer, PaymentProviderIndexMap::NAME, $paymentProviderData[PaymentProviderIndexMap::NAME])
            ->addSearchResultData($pageMapTransfer, PaymentProviderIndexMap::ID_PAYMENT_METHOD, $paymentProviderData[PaymentProviderIndexMap::ID_PAYMENT_METHOD]);

        return $pageMapTransfer;
    }
}