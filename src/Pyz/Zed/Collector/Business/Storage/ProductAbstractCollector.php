<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Business\Storage;

use Everon\Component\Collection\Collection;

use Generated\Shared\Transfer\RawProductAttributesTransfer;
use Generated\Shared\Transfer\StorageProductCategoryTransfer;
use Generated\Shared\Transfer\StorageProductImageTransfer;
use Generated\Shared\Transfer\StorageProductTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryTableMap;
use Orm\Zed\Category\Persistence\SpyCategoryNode;
use Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategory;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Shared\Product\ProductConfig;
use Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface;
use Spryker\Zed\Collector\Business\Collector\Storage\AbstractStoragePdoCollector;
use Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface;
use Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductAbstractCollector extends AbstractStoragePdoCollector
{
    const ID_CATEGORY_NODE = 'id_category_node';
    const SKU = 'sku';
    const URL = 'url';
    const COLOR_CODE = 'color_code';
    const ABSTRACT_ATTRIBUTES = 'abstract_attributes';
    const ABSTRACT_LOCALIZED_ATTRIBUTES = 'abstract_localized_attributes';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const META_KEYWORDS = 'meta_keywords';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const SUPER_ATTRIBUTES_DEFINITION = 'super_attributes_definition';
    const COL_IS_IN_STORE = 'is_in_store';

    /**
     * @var \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface
     */
    protected $categoryQueryContainer;

    /**
     * @var \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    protected $productCategoryQueryContainer;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface
     */
    protected $priceProductFacade;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $categoryCacheCollection;

    /**
     * @var \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected $productImageQueryContainer;

    /**
     * @var \Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface
     */
    protected $productImageFacade;

    /**
     * @var \Spryker\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var array
     */
    protected $superAttributes;

    /**
     * @param \Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface $utilDataReaderService
     * @param \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface $categoryQueryContainer
     * @param \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface $productCategoryQueryContainer
     * @param \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface $productImageQueryContainer
     * @param \Spryker\Zed\Product\Business\ProductFacadeInterface $productFacade
     * @param \Spryker\Zed\PriceProduct\Business\PriceProductFacadeInterface $priceProductFacade
     * @param \Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface $productImageFacade
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        CategoryQueryContainerInterface $categoryQueryContainer,
        ProductCategoryQueryContainerInterface $productCategoryQueryContainer,
        ProductImageQueryContainerInterface $productImageQueryContainer,
        ProductFacadeInterface $productFacade,
        PriceProductFacadeInterface $priceProductFacade,
        ProductImageFacadeInterface $productImageFacade
    ) {
        parent::__construct($utilDataReaderService);

        $this->categoryQueryContainer = $categoryQueryContainer;
        $this->productCategoryQueryContainer = $productCategoryQueryContainer;
        $this->productImageQueryContainer = $productImageQueryContainer;
        $this->priceProductFacade = $priceProductFacade;
        $this->categoryCacheCollection = new Collection([]);
        $this->productFacade = $productFacade;
        $this->productImageFacade = $productImageFacade;
    }

    /**
     * @param array $collectItemData
     *
     * @return bool
     */
    protected function isStorable(array $collectItemData)
    {
        return $collectItemData[static::COL_IS_IN_STORE] !== null;
    }

    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem($touchKey, array $collectItemData)
    {
        $attributes = $this->getAbstractAttributes($collectItemData);

        return [
            StorageProductTransfer::ID_PRODUCT_ABSTRACT => $collectItemData[CollectorConfig::COLLECTOR_RESOURCE_ID],
            StorageProductTransfer::ATTRIBUTES => $this->getAbstractAttributes($collectItemData),
            StorageProductTransfer::NAME => $collectItemData[self::NAME],
            StorageProductTransfer::SKU => $collectItemData[self::SKU],
            StorageProductTransfer::URL => $collectItemData[self::URL],
            StorageProductTransfer::COLOR_CODE => $collectItemData[self::COLOR_CODE],
            StorageProductTransfer::PRICES => $this->getPrices($collectItemData[self::SKU]),
            StorageProductTransfer::CATEGORIES => $this->generateCategories($collectItemData[CollectorConfig::COLLECTOR_RESOURCE_ID]),
            StorageProductTransfer::IMAGE_SETS => $this->generateProductAbstractImageSets(
                $collectItemData[CollectorConfig::COLLECTOR_RESOURCE_ID]
            ),
            StorageProductTransfer::DESCRIPTION => $collectItemData[self::DESCRIPTION],
            StorageProductTransfer::META_TITLE => $collectItemData[self::META_TITLE],
            StorageProductTransfer::META_KEYWORDS => $collectItemData[self::META_KEYWORDS],
            StorageProductTransfer::META_DESCRIPTION => $collectItemData[self::META_DESCRIPTION],
            StorageProductTransfer::SUPER_ATTRIBUTES_DEFINITION => $this->getVariantSuperAttributes($attributes),
        ];
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return ProductConfig::RESOURCE_TYPE_PRODUCT_ABSTRACT;
    }

    /**
     * @param array $collectItemData
     *
     * @return array
     */
    protected function getAbstractAttributes(array $collectItemData)
    {
        $abstractAttributesData = $this->productFacade->decodeProductAttributes($collectItemData[self::ABSTRACT_ATTRIBUTES]);
        $abstractLocalizedAttributesData = $this->productFacade->decodeProductAttributes($collectItemData[self::ABSTRACT_LOCALIZED_ATTRIBUTES]);

        $rawProductAttributesTransfer = new RawProductAttributesTransfer();
        $rawProductAttributesTransfer
            ->setAbstractAttributes($abstractAttributesData)
            ->setAbstractLocalizedAttributes($abstractLocalizedAttributesData);

        $attributes = $this->productFacade->combineRawProductAttributes($rawProductAttributesTransfer);

        $attributes = array_filter($attributes, function ($key) {
            return !empty($key);
        }, ARRAY_FILTER_USE_KEY);

        return $attributes;
    }

    /**
     * @param string $sku
     *
     * @return array
     */
    protected function getPrices($sku)
    {
        return $this->priceProductFacade->findPricesBySkuGroupedForCurrentStore($sku);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function generateCategories($idProductAbstract)
    {
        if ($this->categoryCacheCollection->has($idProductAbstract)) {
            return $this->categoryCacheCollection->get($idProductAbstract);
        }

        $productCategoryMappings = $this->getProductCategoryMappings($idProductAbstract);

        $categories = [];
        foreach ($productCategoryMappings as $mapping) {
            $categories = $this->generateProductCategoryData($mapping, $categories);
        }

        $this->categoryCacheCollection->set($idProductAbstract, $categories);

        return $categories;
    }

    /**
     * @param \Orm\Zed\ProductCategory\Persistence\SpyProductCategory $productCategory
     * @param array $productCategoryCollection
     *
     * @return array
     */
    protected function generateProductCategoryData(SpyProductCategory $productCategory, array $productCategoryCollection)
    {
        foreach ($productCategory->getSpyCategory()->getNodes() as $node) {
            $queryPath = $this->categoryQueryContainer->queryPath($node->getIdCategoryNode(), $this->locale->getIdLocale());
            $pathTokens = $queryPath->find();

            $productCategoryCollection = $this->generateCategoryData($pathTokens, $productCategoryCollection);
        }

        return $productCategoryCollection;
    }

    /**
     * @param array $pathTokens
     * @param array $productCategoryCollection
     *
     * @return array
     */
    protected function generateCategoryData(array $pathTokens, array $productCategoryCollection)
    {
        foreach ($pathTokens as $pathItem) {
            $idNode = (int)$pathItem[self::ID_CATEGORY_NODE];
            $url = $this->generateUrl($idNode);

            $productCategoryCollection[$idNode] = [
                StorageProductCategoryTransfer::NODE_ID => $idNode,
                StorageProductCategoryTransfer::NAME => $pathItem[self::NAME],
                StorageProductCategoryTransfer::URL => $url,
            ];
        }

        return $productCategoryCollection;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return \Orm\Zed\ProductCategory\Persistence\SpyProductCategory[]|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function getProductCategoryMappings($idProductAbstract)
    {
        return $this->productCategoryQueryContainer
            ->queryLocalizedProductCategoryMappingByIdProduct($idProductAbstract)
            ->innerJoinSpyCategory()
            ->addAnd(
                SpyCategoryTableMap::COL_IS_ACTIVE,
                true,
                Criteria::EQUAL
            )
            ->orderByProductOrder()
            ->find();
    }

    /**
     * @param int $idNode
     *
     * @return null|string
     */
    protected function generateUrl($idNode)
    {
        $urlQuery = $this->categoryQueryContainer
            ->queryUrlByIdCategoryNode($idNode)
            ->filterByFkLocale($this->locale->getIdLocale());

        $url = $urlQuery->findOne();
        return ($url ? $url->getUrl() : null);
    }

    /**
     * @param \Orm\Zed\Category\Persistence\SpyCategoryNode $node
     *
     * @return string
     */
    protected function buildPath(SpyCategoryNode $node)
    {
        $pathTokens = $this->categoryQueryContainer
            ->queryPath($node->getIdCategoryNode(), $this->locale->getIdLocale(), false, true)
            ->find();

        $formattedPath = [];
        foreach ($pathTokens as $path) {
            $formattedPath[] = $path[self::NAME];
        }

        return '/' . implode('/', $formattedPath);
    }

    /**
     * @return \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface
     */
    public function getCategoryQueryContainer()
    {
        return $this->categoryQueryContainer;
    }

    /**
     * @param \Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface $categoryQueryContainer
     *
     * @return void
     */
    public function setCategoryQueryContainer(CategoryQueryContainerInterface $categoryQueryContainer)
    {
        $this->categoryQueryContainer = $categoryQueryContainer;
    }

    /**
     * @return \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface
     */
    public function getProductCategoryQueryContainer()
    {
        return $this->productCategoryQueryContainer;
    }

    /**
     * @param \Spryker\Zed\ProductCategory\Persistence\ProductCategoryQueryContainerInterface $productCategoryQueryContainer
     *
     * @return void
     */
    public function setProductCategoryQueryContainer(ProductCategoryQueryContainerInterface $productCategoryQueryContainer)
    {
        $this->productCategoryQueryContainer = $productCategoryQueryContainer;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function generateProductAbstractImageSets($idProductAbstract)
    {
        $imageSetTransfers = $this->productImageFacade->getCombinedAbstractImageSets(
            $idProductAbstract,
            $this->locale->getIdLocale()
        );

        $result = [];

        foreach ($imageSetTransfers as $imageSetTransfer) {
            foreach ($imageSetTransfer->getProductImages() as $productImageTransfer) {
                $result[$imageSetTransfer->getName()][] = [
                    StorageProductImageTransfer::ID_PRODUCT_IMAGE => $productImageTransfer->getIdProductImage(),
                    StorageProductImageTransfer::EXTERNAL_URL_LARGE => $productImageTransfer->getExternalUrlLarge(),
                    StorageProductImageTransfer::EXTERNAL_URL_SMALL => $productImageTransfer->getExternalUrlSmall(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function getVariantSuperAttributes(array $attributes)
    {
        if (!$this->superAttributes) {
            $superAttributes = SpyProductAttributeKeyQuery::create()
                ->filterByIsSuper(true)
                ->find();

            foreach ($superAttributes as $attribute) {
                $this->superAttributes[$attribute->getKey()] = true;
            }
        }

        return $this->filterVariantSuperAttributes($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function filterVariantSuperAttributes(array $attributes)
    {
        $variantSuperAttributes = array_filter($attributes, function ($key) {
            return isset($this->superAttributes[$key]);
        }, ARRAY_FILTER_USE_KEY);

        return array_keys($variantSuperAttributes);
    }
}
