<?php
/**
 * Durst - project - CategoryList.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.05.18
 * Time: 11:21
 */

namespace Pyz\Zed\Category\Business\Model\CategoryList;


use Generated\Shared\Transfer\CategoryLocalizedAttributesTransfer;
use Generated\Shared\Transfer\CategoryTransfer;
use Orm\Zed\Category\Persistence\SpyCategory;
use Orm\Zed\Category\Persistence\SpyCategoryAttribute;
use Pyz\Zed\Category\Persistence\CategoryQueryContainerInterface;

class CategoryList
{
    /**
     * @var CategoryQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * CategoryList constructor.
     * @param CategoryQueryContainerInterface $queryContainer
     */
    public function __construct(CategoryQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idLocale
     * @return array|CategoryTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCategoryList(int $idLocale) : array
    {
        $entities = $this
            ->queryContainer
            ->queryAllCategories()
            ->filterByIsActive(true)
            ->filterByIsInMenu(true)
            ->filterByIsSearchable(true)
            ->useAttributeQuery()
                ->filterByFkLocale($idLocale)
            ->endUse()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param SpyCategory $entity
     * @return CategoryTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function entityToTransfer(SpyCategory $entity) : CategoryTransfer
    {
        $transfer = (new CategoryTransfer())
            ->fromArray($entity->toArray(), true);

        foreach ($entity->getAttributes() as $attribute) {
            $transfer->addLocalizedAttributes($this->attributeEntityToTransfer($attribute));
        }

        return $transfer;
    }

    /**
     * @param SpyCategoryAttribute $entity
     * @return CategoryLocalizedAttributesTransfer
     */
    protected function attributeEntityToTransfer(SpyCategoryAttribute $entity) : CategoryLocalizedAttributesTransfer
    {
        return (new CategoryLocalizedAttributesTransfer())
            ->fromArray($entity->toArray(), true);
    }

}