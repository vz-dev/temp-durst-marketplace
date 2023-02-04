<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProduct.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.06.21
 * Time: 15:42
 */

namespace Pyz\Zed\Campaign\Business\Model;


use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderProductNotFoundException;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignPeriodBranchOrderProduct implements CampaignPeriodBranchOrderProductInterface
{
    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface[]
     */
    protected $hydrators;

    /**
     * CampaignPeriodBranchOrderProduct constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface[] $hydrators
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        array $hydrators
    )
    {
        $this->queryContainer = $queryContainer;
        $this->hydrators = $hydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @return array|\Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getProductsForCampaignAndBranch(int $idCampaignPeriod, int $idBranch): array
    {
        $products = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProduct()
            ->useDstCampaignPeriodBranchOrderQuery()
                ->filterByFkBranch(
                    $idBranch
                )
                ->filterByFkCampaignPeriod(
                    $idCampaignPeriod
                )
            ->endUse()
            ->find();

        $result = [];

        foreach ($products as $product) {
            $result[] = $this
                ->entityToTransfer(
                    $product
                );
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriodBranchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodBranchOrderProductNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignOrderProductById(
        int $idCampaignPeriodBranchOrderProduct
    ): CampaignPeriodBranchOrderProductTransfer
    {
        $product = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProduct()
            ->filterByIdCampaignPeriodBranchOrderProduct(
                $idCampaignPeriodBranchOrderProduct
            )
            ->findOne();

        if ($product === null) {
            throw new CampaignPeriodBranchOrderProductNotFoundException(
                sprintf(
                    CampaignPeriodBranchOrderProductNotFoundException::MESSAGE,
                    $idCampaignPeriodBranchOrderProduct
                )
            );
        }

        return $this
            ->entityToTransfer(
                $product
            );
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriodBranchOrderProduct $branchOrderProduct
     * @return \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function entityToTransfer(DstCampaignPeriodBranchOrderProduct $branchOrderProduct): CampaignPeriodBranchOrderProductTransfer
    {
        $transfer = new CampaignPeriodBranchOrderProductTransfer();

        $transfer
            ->fromArray(
                $branchOrderProduct
                    ->toArray(),
                true
            );

        $transfer
            ->setFkBranch(
                $branchOrderProduct
                    ->getDstCampaignPeriodBranchOrder()
                    ->getFkBranch()
            )
            ->setFkCampaignPeriod(
                $branchOrderProduct
                    ->getDstCampaignPeriodBranchOrder()
                    ->getFkCampaignPeriod()
            );

        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrateCampaignPeriodBranchOrderProduct(
                    $transfer
                );
        }

        return $transfer;
    }
}
