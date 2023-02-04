<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 09.11.18
 * Time: 15:45
 */

namespace Pyz\Zed\MerchantPrice\Communication\Plugin;


use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

/**
 * Class PriceDataPageMapPlugin
 * @package Pyz\Zed\MerchantPrice\Communication\Plugin
 * @method MerchantPriceFacadeInterface getFacade()
 */
class PriceDataPageMapPlugin extends AbstractPlugin implements PageMapInterface
{

    /**
     * @api
     *
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $data
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $data, LocaleTransfer $locale) : PageMapTransfer
    {
        return $this
            ->getFacade()
            ->buildPageMap($pageMapBuilder, $data, $locale);
    }
}