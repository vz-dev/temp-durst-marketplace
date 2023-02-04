<?php
/**
 * Durst - project - GMTimeslotDataPageMapPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.10.21
 * Time: 07:35
 */

namespace Pyz\Zed\GraphMasters\Communication\Plugin;


use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

/**
 * Class GMTimeslotDataPageMapPlugin
 * @package Pyz\Zed\GraphMasters\Communication\Plugin
 * @method GraphMastersFacadeInterface getFacade()
 */
class GMTimeslotDataPageMapPlugin extends AbstractPlugin implements PageMapInterface
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
            ->buildTimeslotPageMap($pageMapBuilder, $data, $locale);
    }
}
