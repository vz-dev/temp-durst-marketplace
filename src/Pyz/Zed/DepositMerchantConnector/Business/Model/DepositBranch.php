<?php
/**
 * Durst - project - DepositBranch.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:32
 */

namespace Pyz\Zed\DepositMerchantConnector\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DepositTransfer;
use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridgeInterface;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridgeInterface;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;

class DepositBranch implements DepositBranchInterface
{
    protected const KEY_GTIN = 'gtin';

    protected const PRODUCT_RELATION = 'productRelation';
    protected const LOCALIZED_ATTRIBUTES_RELATION = 'localizedAttributesRelation';

    /**
     * @var \Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridgeInterface
     */
    protected $depositQueryContainer;

    /**
     * @var \Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridgeInterface
     */
    protected $merchantPriceQueryContainer;

    /**
     * @var \Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface
     */
    protected $localeFacade;

    /**
     * @var TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var float
     */
    protected $currentTaxRate;

    /**
     * @var int[]
     */
    protected $productIds = [];

    /**
     * DepositBranch constructor.
     *
     * @param \Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridgeInterface $depositQueryContainer
     * @param \Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridgeInterface $merchantPriceQueryContainer
     * @param \Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface $localeFacade
     */
    public function __construct(
        DepositMerchantConnectorToDepositBridgeInterface $depositQueryContainer,
        DepositMerchantConnectorToMerchantPriceBridgeInterface $merchantPriceQueryContainer,
        DepositMerchantConnectorToLocaleBridgeInterface $localeFacade,
        TaxFacadeInterface $taxFacade
    ) {
        $this->depositQueryContainer = $depositQueryContainer;
        $this->merchantPriceQueryContainer = $merchantPriceQueryContainer;
        $this->localeFacade = $localeFacade;
        $this->taxFacade = $taxFacade;

        $this->currentTaxRate = $this->taxFacade->getDefaultTaxRate();
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): array
    {
        $entities = $this
            ->depositQueryContainer
            ->queryDeposit()
            ->useDstBranchToDepositQuery()
                ->filterByFkBranch($branchTransfer->getIdBranch())
            ->endUse()
            ->useSpyProductQuery(self::PRODUCT_RELATION, Criteria::LEFT_JOIN)
                ->useSpyProductLocalizedAttributesQuery(self::LOCALIZED_ATTRIBUTES_RELATION, Criteria::LEFT_JOIN)
                    ->filterByFkLocale(null, Criteria::ISNULL)
                    ->_or()
                    ->filterByFkLocale($this->localeFacade->getCurrentLocale()->getIdLocale())
                ->endUse()
            ->endUse()
            ->with(self::PRODUCT_RELATION)
            ->with(self::LOCALIZED_ATTRIBUTES_RELATION)
            ->orderByIdDeposit(Criteria::ASC)
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->depositEntityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $entity
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    protected function depositEntityToTransfer(SpyDeposit $entity): DepositTransfer
    {
        $transfer = (new DepositTransfer())
            ->fromArray($entity->toArray(), true);

        $transfer
            ->setDepositB2b($this->getDepositValueWithTax($entity->getDeposit()))
            ->setDepositCaseB2b($this->getDepositValueWithTax($entity->getDepositCase()))
            ->setDepositPerBottleB2b($this->getDepositValueWithTax($entity->getDepositPerBottle()));

        $gtins = [];
        foreach ($entity->getSpyProducts() as $product) {
            if ($product->getSpyProductLocalizedAttributess() !== null) {
                foreach ($product->getSpyProductLocalizedAttributess() as $localizedAttributes) {
                    $attributes = json_decode(
                        $localizedAttributes->getAttributes(),
                        true
                    );
                    if (isset($attributes[self::KEY_GTIN])) {
                        $gtins[$attributes[self::KEY_GTIN]] = $attributes[self::KEY_GTIN];
                    }
                }
            }
        }

        return $transfer->setGtins(array_values($gtins));
    }

    /**
     * @param int $depositValue
     * @return int
     */
    protected function getDepositValueWithTax(int $depositValue) : int
    {
        $taxRate = (100 + $this->currentTaxRate) / 100;

        return round($depositValue * $taxRate);
    }
}
