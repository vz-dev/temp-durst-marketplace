<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 08.11.18
 * Time: 10:22
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin;


use Generated\Shared\Transfer\LocaleTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

/**
 * Class DeliveryAreaDataPageMapPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class DeliveryAreaDataPageMapPlugin extends AbstractPlugin implements PageMapInterface
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
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $data, LocaleTransfer $locale)
    {
        return $this
            ->getFacade()
            ->buildDeliveryAreaPageMap($pageMapBuilder, $data, $locale);
    }
}