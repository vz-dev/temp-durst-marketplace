<?php
/**
 * Durst - project - BranchProductPriceAggregator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.05.18
 * Time: 11:37
 */

namespace Pyz\Zed\MerchantPrice\Business\Model;

use ArrayObject;
use Generated\Shared\Transfer\CatalogCategoryTransfer;
use Generated\Shared\Transfer\CatalogPriceTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\CatalogUnitTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryNodeTableMap;
use Orm\Zed\Category\Persistence\SpyCategory;
use Orm\Zed\Category\Persistence\SpyCategoryAttribute;
use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceArchiveQuery;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;

class Catalog
{
    protected const KEY_PRODUCT_NAME = 'name';
    protected const KEY_PRODUCT_INGREDIENTS = 'ingredients';
    protected const KEY_PRODUCT_NUTRITIONAL_VALUES = 'nutrition_facts';
    protected const KEY_PRODUCT_DESCRIPTION = 'description';
    protected const KEY_ALLERGENS = 'allergens';
    protected const KEY_ORGANIC = 'organic';
    protected const KEY_IMAGE_BOTTLE = 'bottleshot_big';
    protected const KEY_IMAGE_DETAIL_1 = 'picture_detail_1';
    protected const KEY_IMAGE_DETAIL_2 = 'picture_detail_2';
    protected const KEY_IMAGE_DETAIL_3 = 'picture_detail_3';
    protected const KEY_IMAGE_DETAIL_4 = 'picture_detail_4';
    protected const KEY_IMAGE_DETAIL_5 = 'picture_detail_5';
    protected const KEY_IMAGE_PRODUCT_LOGO = 'product_logo';
    protected const KEY_MANUFACTURER_ID = 'manufacturer';
    protected const KEY_ALCOHOL_BY_VOLUME = 'alcohol_by_volume';
    protected const KEY_TAGS = 'tags';
    protected const KEY_BIO_CONTROL_AUTHORITY = 'bio_control_authority';
    protected const KEY_UNIT_IMAGE_BOTTLE = 'bottleshot_product_unit';
    protected const KEY_UNIT_IMAGE_CASE = 'caseshot_product_unit';
    protected const KEY_FAT = 'fat';
    protected const KEY_KILOJOULES = 'kilojoules';
    protected const KEY_INGREDIENTS = 'ingredients';
    protected const KEY_HEREOF_SUGAR = 'hereof_sugar';
    protected const KEY_KILOCALORIES = 'kilocalories';
    protected const KEY_CARBOHYDRATES = 'carbohydrates';
    protected const KEY_HEREOF_SATURATED_FATTY_ACIDS = 'hereof_saturated_fatty_acids';
    protected const KEY_SALT = 'salt';
    protected const KEY_PROTEINS = 'proteins';

    protected const KEY_UNIT_MATERIAL = 'material';
    protected const KEY_UNIT_VOLUME_PER_BOTTLE = 'volume_per_bottle';
    protected const KEY_UNIT_BOTTLES = 'bottles';

    /**
     * @var \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $currentLocale;

    /**
     * @var \Generated\Shared\Transfer\CatalogCategoryTransfer[]
     */
    protected $catalogCategories = [];

    /**
     * @var \Generated\Shared\Transfer\CatalogCategoryTransfer[]
     */
    protected $nodeCategories = [];

    /**
     * @var array
     */
    protected $allParentNodes = [];

    /**
     * @var \Generated\Shared\Transfer\CatalogPriceTransfer[]
     */
    protected $prices = [];

    /**
     * @var \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface
     */
    protected $taxAmountCalculator;

    /**
     * Catalog constructor.
     *
     * @param \Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainerInterface $queryContainer
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $utilEncodingService
     * @param \Generated\Shared\Transfer\LocaleTransfer $currentLocale
     * @param \Pyz\Zed\MerchantPrice\Business\Model\Helper\TaxAmountCalculatorInterface $taxAmountCalculator
     */
    public function __construct(
        MerchantPriceQueryContainerInterface $queryContainer,
        UtilEncodingServiceInterface $utilEncodingService,
        LocaleTransfer $currentLocale,
        TaxAmountCalculatorInterface $taxAmountCalculator
    ) {
        $this->queryContainer = $queryContainer;
        $this->utilEncodingService = $utilEncodingService;
        $this->currentLocale = $currentLocale;
        $this->taxAmountCalculator = $taxAmountCalculator;
    }

    /**
     * @param array $branchIds
     *
     * @return \Generated\Shared\Transfer\CatalogCategoryTransfer[]|\ArrayObject
     */
    public function getCatalogForBranches(array $branchIds)
    {
        $priceEntities = $this
            ->queryContainer
            ->queryActivePricesAndProductsForBranches(
                $branchIds,
                $this->currentLocale->getIdLocale()
            )
            ->find();

        $this->setAllParentNodes();

        foreach ($priceEntities as $priceEntity) {
            foreach ($priceEntity->getSpyProduct()->getSpyProductAbstract()->getSpyProductCategories() as $productCategoryEntity) {
                $category = $this->getCategory($productCategoryEntity->getSpyCategory());
                $subCategory = $this->getSubCategory($productCategoryEntity->getSpyCategory());
                if ($subCategory !== false) {
                    $subProduct = $this->getProduct($subCategory, $priceEntity->getSpyProduct()->getSpyProductAbstract());
                    $subUnit = $this->getUnit($subProduct, $priceEntity->getSpyProduct());
                    $this->addPriceTransfer($subUnit, $priceEntity);
                }

                $product = $this->getProduct($category, $priceEntity->getSpyProduct()->getSpyProductAbstract());
                $unit = $this->getUnit($product, $priceEntity->getSpyProduct());
                $this->addPriceTransfer($unit, $priceEntity);
            }
        }

        return $this->purgeOffsets();
    }

    /**
     * @param int $idBranch
     * @param string $sku
     *
     * @return CatalogProductTransfer
     *
     * @throws PropelException
     */
    public function getCatalogProductForBranchBySku(int $idBranch, string $sku, string $concreteSku = null,  bool $deactivated = false, bool $archive = false)
    {
        $priceEntities = $this
            ->queryContainer
            ->queryActivePricesAndDepositsForProductByIdBranchAndSku($idBranch, $sku, $deactivated)
            ->find();

        $firstProduct = $priceEntities->getFirst();
        if ($firstProduct == null && $archive == true){
            $firstPrice = MerchantPriceArchiveQuery::create()
                ->filterBySku($concreteSku . '_' . $idBranch)
                ->filterByFkBranch($idBranch)
            ->findOne();

            $abstractProduct = $this
                ->queryContainer
                ->queryProducts()
                    ->filterByIdProduct($firstPrice->getFkProduct())
                ->findOne()
                ->getSpyProductAbstract();
        } else {
            $abstractProduct = $firstProduct->getSpyProduct()->getSpyProductAbstract();
        }

        $product = $this->createProductTransfer(
            $abstractProduct
        );

        foreach ($priceEntities as $priceEntity) {
            $price = $this->createPriceTransfer($priceEntity);

            $unit = $this->createUnitTransfer($priceEntity->getSpyProduct());
            $unit->addPrices($price);

            $product->addUnits($unit);
        }

        return $product;
    }

    /**
     * Dearest future reader,
     *
     * you might ask why I implemented this (seemingly unnecessary) method.
     * See, the thing is that somebody obviously didn't do their job very well, which leads
     * to a problem where serialized ArrayObjects would not be unserialized properly, if
     * the array keys (or offsets) were set manually.
     *
     * Therefore I need to remove those keys before returning the collection.
     *
     * I am deeply sorry for the inconvenience.
     *
     * Best and may the force be with you,
     * Mathias
     *
     * @return \ArrayObject
     */
    protected function purgeOffsets(): ArrayObject
    {
        $catalog = new ArrayObject();

        foreach ($this->catalogCategories as $catalogCategory) {
            $catalog[] = $catalogCategory;

            if (in_array($catalogCategory->getIdCategory(), $this->getAllParentNodes())) {
                $subcategories = $this->nodeCategories;
                $catalogCategory->setSubCategory(new ArrayObject());
                foreach ($subcategories as $subcategory) {
                    $catalogCategory->addSubCategory($subcategory);

                    $subcategoryProducts = $subcategory->getProducts();
                    $subcategory->setProducts(new ArrayObject());
                    $this->purgeProductUnitPricesOffset($subcategoryProducts, $subcategory);
                }
            }

            $categoryProducts = $catalogCategory->getProducts();
            $catalogCategory->setProducts(new ArrayObject());
            $this->purgeProductUnitPricesOffset($categoryProducts, $catalogCategory);

        }

        return $catalog;
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $entity
     *
     * @return \Generated\Shared\Transfer\CatalogPriceTransfer
     */
    protected function createPriceTransfer(MerchantPrice $entity): CatalogPriceTransfer
    {
        return (new CatalogPriceTransfer())
            ->setIdBranch($entity->getFkBranch())
            ->setPrice($entity->getGrossPrice())
            ->setUnitPrice($this->calculateUnitPrice($entity, $entity->getGrossPrice()))
            ->setStatus($entity->getStatus());
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $entity
     * @param int $price
     *
     * @return int
     */
    protected function calculateUnitPrice(MerchantPrice $entity, int $price): int
    {
        $deposit = $entity->getSpyProduct()->getSpyDeposit();

        if ($deposit->getBottles() === null || $deposit->getVolumePerBottle() === null) {
            return 0;
        }

        $sumVolume = $deposit->getBottles() * $deposit->getVolumePerBottle();

        if ($sumVolume === 0) {
            return 0;
        }

        return (int)round(($price / ($sumVolume / 1000.0)));
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogUnitTransfer $unit
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $entity
     *
     * @return \Generated\Shared\Transfer\CatalogPriceTransfer
     */
    protected function addPriceTransfer(CatalogUnitTransfer $unit, MerchantPrice $entity)
    {
        if ($unit->getPrices()->offsetExists($entity->getIdPrice()) !== true) {
            $unit->getPrices()->offsetSet($entity->getIdPrice(), $this->createPriceTransfer($entity));
        }

        return $unit->getPrices()->offsetGet($entity->getIdPrice());
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogProductTransfer $product
     * @param \Orm\Zed\Product\Persistence\SpyProduct $entity
     *
     * @return \Generated\Shared\Transfer\CatalogUnitTransfer
     */
    protected function getUnit(CatalogProductTransfer $product, SpyProduct $entity): CatalogUnitTransfer
    {
        if ($product->getUnits()->offsetExists($entity->getIdProduct()) !== true) {
            $product->getUnits()->offsetSet($entity->getIdProduct(), $this->createUnitTransfer($entity));
        }

        return $product->getUnits()->offsetGet($entity->getIdProduct());
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $entity
     *
     * @return \Generated\Shared\Transfer\CatalogUnitTransfer
     */
    protected function createUnitTransfer(SpyProduct $entity): CatalogUnitTransfer
    {
        $attributes = $this->utilEncodingService->decodeJson($entity->getAttributes(), true);
        $deposit = $entity->getSpyDeposit();

        return (new CatalogUnitTransfer())
            ->setName($this->getDepositName($deposit))
            ->setSku($entity->getSku())
            ->setCode($deposit->getCode())
            ->setDeposit($deposit->getDeposit())
            ->setVolume($this->calculateVolume($deposit))
            ->setUrlUnitImageBottle($this->getAttribute($attributes, self::KEY_UNIT_IMAGE_BOTTLE))
            ->setUrlUnitImageCase($this->getAttribute($attributes, self::KEY_UNIT_IMAGE_CASE))
            ->setMaterial($deposit->getMaterial())
            ->setVolumePerBottle($deposit->getVolumePerBottle())
            ->setBottles($deposit->getBottles())
            ->setDepositType($deposit->getDepositType())
            ->setWeight($deposit->getWeight())
            ->setRelevance($entity->getMerchantPrices()[0]->getSortItems());
    }

    /**
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $entity
     *
     * @return string
     */
    protected function getDepositName(SpyDeposit $entity): string
    {
        if ($entity->getPresentationName() !== null) {
            return $entity->getPresentationName();
        }

        return $entity->getName();
    }

    /**
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $entity
     *
     * @return int
     */
    protected function calculateVolume(SpyDeposit $entity): int
    {
        return $entity->getVolumePerBottle() * $entity->getBottles();
    }

    /**
     * @param \Orm\Zed\Category\Persistence\SpyCategory $categoryEntity
     *
     * @return \Generated\Shared\Transfer\CatalogCategoryTransfer
     */
    protected function getCategory(SpyCategory $categoryEntity): CatalogCategoryTransfer
    {
        if (isset($this->catalogCategories[$categoryEntity->getIdCategory()]) !== true) {
            $this->catalogCategories[$categoryEntity->getIdCategory()] = $this->createCategoryTransfer($categoryEntity);
        }

        return $this->catalogCategories[$categoryEntity->getIdCategory()];
    }

    /**
     * @param \Orm\Zed\Category\Persistence\SpyCategory $categoryEntity
     *
     * @return \Generated\Shared\Transfer\CatalogCategoryTransfer | bool
     */
    protected function getSubCategory(SpyCategory $categoryEntity)
    {
        $testNodes = $categoryEntity->getNodes();
        foreach ($testNodes as $node) {
            if ($node->getIsRoot() !== true ) {
                $parent = $node->getParentCategoryNode();
                if ($parent->getIsRoot() === true) {
                    return false;
                } else {
                    if (isset($this->nodeCategories[$node->getFkCategory()]) !== true) {
                        $this->nodeCategories[$node->getFkCategory()] = $this->createCategoryTransfer($node->getCategory());
                    }

                    return $this->nodeCategories[$node->getFkCategory()];
                }
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogCategoryTransfer $category
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $entity
     *
     * @return \Generated\Shared\Transfer\CatalogProductTransfer
     */
    protected function getProduct(CatalogCategoryTransfer $category, SpyProductAbstract $entity): CatalogProductTransfer
    {
        if ($category->getProducts()->offsetExists($entity->getIdProductAbstract()) !== true) {
            $category->getProducts()->offsetSet($entity->getIdProductAbstract(), $this->createProductTransfer($entity));
        }

        return $category->getProducts()->offsetGet($entity->getIdProductAbstract());
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $entity
     *
     * @return \Generated\Shared\Transfer\CatalogProductTransfer
     */
    protected function createProductTransfer(SpyProductAbstract $entity): CatalogProductTransfer
    {
        $attributes = $this->utilEncodingService->decodeJson($entity->getAttributes(), true);
        $localizedAttributes = '[]';
        if ($entity->getSpyProductAbstractLocalizedAttributess()->count() > 0) {
            $localizedAttributes = $entity->getSpyProductAbstractLocalizedAttributess()->getFirst()->getAttributes();
        }
        $localizedAttributes = $this->utilEncodingService->decodeJson($localizedAttributes, true);
        $manufacturer = $entity->getSpyManufacturer();
        $productTransfer = (new CatalogProductTransfer())
            ->setSku($entity->getSku())
            ->setName($this->getAttribute($attributes, self::KEY_PRODUCT_NAME))
            ->setNutritionalValues($this->getAttribute($attributes, self::KEY_PRODUCT_NUTRITIONAL_VALUES))
            ->setIngredients($this->getAttribute($attributes, self::KEY_PRODUCT_INGREDIENTS))
            ->setUrlImageBottle($this->getAttribute($attributes, self::KEY_IMAGE_BOTTLE))
            ->setUrlImageList($this->getImageList($attributes))
            ->setUrlImageThumb($this->getAttribute($attributes, self::KEY_IMAGE_BOTTLE))
            ->setUrlProductLogo($this->getAttribute($attributes, self::KEY_IMAGE_PRODUCT_LOGO))
            ->setDescription($this->getAttribute($attributes, self::KEY_PRODUCT_DESCRIPTION))
            ->setAllergens($this->getAttribute($attributes, self::KEY_ALLERGENS))
            ->setAlcoholAmount($this->getAttribute($attributes, self::KEY_ALCOHOL_BY_VOLUME))
            ->setTags($this->getAttributeAsArray($this->getAttribute($localizedAttributes, self::KEY_TAGS)))
            ->setBioControlAuthority($this->getAttribute($localizedAttributes, self::KEY_BIO_CONTROL_AUTHORITY))
            ->setFat($this->getAttribute($attributes, self::KEY_FAT))
            ->setKilojoules($this->getAttribute($attributes, self::KEY_KILOJOULES))
            ->setHereofSugar($this->getAttribute($attributes, self::KEY_HEREOF_SUGAR))
            ->setKilocalories($this->getAttribute($attributes, self::KEY_KILOCALORIES))
            ->setCarbohydrates($this->getAttribute($attributes, self::KEY_CARBOHYDRATES))
            ->setAlcoholByVolume($this->getAttribute($attributes, self::KEY_ALCOHOL_BY_VOLUME))
            ->setHereofSaturatedFattyAcids($this->getAttribute($attributes, self::KEY_HEREOF_SATURATED_FATTY_ACIDS))
            ->setSalt($this->getAttribute($attributes, self::KEY_SALT))
            ->setProteins($this->getAttribute($attributes, self::KEY_PROTEINS));

        if ($manufacturer !== null) {
            $productTransfer
                ->setManufacturerName($manufacturer->getName())
                ->setManufacturerLogoUrl($manufacturer->getLogoUrl())
                ->setManufacturerAddress1($manufacturer->getAddress2())
                ->setManufacturerAddress2($manufacturer->getAddress3());
        }

        return $productTransfer;
    }

    /**
     * @param null|string $attribute
     *
     * @return array
     */
    protected function getAttributeAsArray(?string $attribute): array
    {
        if ($attribute === null) {
            return [];
        }

        $array = explode(',', $attribute);

        foreach ($array as $key => $item) {
            $array[$key] = trim($item);
        }

        return $array;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function getImageList(array $attributes): array
    {
        $imageList = [];
        if ($this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_1) !== null) {
            $imageList[] = $this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_1);
        }
        if ($this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_2) !== null) {
            $imageList[] = $this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_2);
        }
        if ($this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_3) !== null) {
            $imageList[] = $this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_3);
        }
        if ($this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_4) !== null) {
            $imageList[] = $this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_4);
        }
        if ($this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_5) !== null) {
            $imageList[] = $this->getAttribute($attributes, self::KEY_IMAGE_DETAIL_5);
        }

        return $imageList;
    }

    /**
     * @param array $attributes
     * @param string $key
     *
     * @return string|null
     */
    protected function getAttribute(array $attributes, string $key)
    {
        if (array_key_exists($key, $attributes)) {
            return $attributes[$key];
        }

        return null;
    }

    /**
     * @param \Orm\Zed\Category\Persistence\SpyCategory $entity
     *
     * @return \Generated\Shared\Transfer\CatalogCategoryTransfer
     */
    protected function createCategoryTransfer(SpyCategory $entity): CatalogCategoryTransfer
    {
        /** @var SpyCategoryAttribute $attribute */
        $attribute = $entity->getAttributes()->getFirst();

        return (new CatalogCategoryTransfer())
            ->setName($attribute->getName())
            ->setIdCategory($entity->getIdCategory())
            ->setImageUrl($attribute->getImageUrl())
            ->setColorCode($attribute->getColorCode())
            ->setPriority($attribute->getPriority())
            ->setHasSubCategories(in_array($entity->getIdCategory(), $this->getAllParentNodes()))
            ->setFkParentCategory($entity->getNodes()[0]->getFkParentCategoryNode());
    }

    /**
     * Function setter for the allParentNodes, value of which is an array of all the parent category ids
     *
     * @throws PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    private function setAllParentNodes()
    {
        $this->allParentNodes = $this
            ->queryContainer
            ->queryPrices()
            ->useSpyProductQuery()
                ->useSpyProductAbstractQuery()
                    ->useSpyProductCategoryQuery()
                        ->useSpyCategoryQuery()
                            ->useNodeQuery()
                                ->filterByIsRoot(false)
                                ->groupByFkParentCategoryNode()
                            ->endUse()
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->select([
                SpyCategoryNodeTableMap::COL_FK_PARENT_CATEGORY_NODE
            ])
            ->find()
            ->getData();
    }

    /**
     * Returns the array of all the parent category ids
     *
     * @return array
     */
    private function getAllParentNodes()
    {
        return $this->allParentNodes;
    }

    /**
     * For more info see: purgeOffset() function. This is just an extension for code re-usability.
     *
     * @param $products
     * @param $category
     */
    private function purgeProductUnitPricesOffset($products, $category)
    {
        foreach ($products as $product) {
            $category->addProducts($product);

            $units = $product->getUnits();
            $product->setUnits(new ArrayObject());
            foreach ($units as $unit) {
                $product->addUnits($unit);

                $prices = $unit->getPrices();
                $unit->setPrices(new ArrayObject());
                foreach ($prices as $price) {
                    $unit->addPrices($price);
                }
            }
        }
    }
}
