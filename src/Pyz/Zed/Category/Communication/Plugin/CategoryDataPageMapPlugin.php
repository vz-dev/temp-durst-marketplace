<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 12.11.18
 * Time: 11:01
 */

namespace Pyz\Zed\Category\Communication\Plugin;



use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

/**
 * Class CategoryDataPageMapPlugin
 * @package Pyz\Zed\Category\Communication\Plugin
 * @method CategoryFacadeInterface getFacade()
 */
class CategoryDataPageMapPlugin extends AbstractPlugin implements PageMapInterface
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
            ->buildCategoryPageMap($pageMapBuilder, $data, $locale);
    }
}