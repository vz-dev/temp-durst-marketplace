<?php
/**
 * Durst - project - BillingItem.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 22:51
 */

namespace Pyz\Zed\Billing\Business\Model;

use Generated\Shared\Transfer\BillingItemTransfer;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;
use Orm\Zed\Billing\Persistence\DstBillingItem;
use Orm\Zed\Billing\Persistence\DstBillingItemTaxRateTotal;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;

class BillingItem implements BillingItemInterface
{
    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface
     */
    protected $billingPeriodModel;

    /**
     * BillingItem constructor.
     *
     * @param \Pyz\Zed\Billing\BillingConfig $config
     * @param \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface $billingPeriodModel
     */
    public function __construct(BillingConfig $config, BillingQueryContainerInterface $queryContainer, BillingPeriodInterface $billingPeriodModel)
    {
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->billingPeriodModel = $billingPeriodModel;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingItemTransfer $billingItemTransfer
     *
     * @return \Generated\Shared\Transfer\BillingItemTransfer
     */
    public function createBillingItem(BillingItemTransfer $billingItemTransfer): BillingItemTransfer
    {
        return $this->createBillingEntity($billingItemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\BillingItemTransfer $billingItemTransfer
     *
     * @return \Generated\Shared\Transfer\BillingItemTransfer
     */
    protected function createBillingEntity(BillingItemTransfer $billingItemTransfer) : BillingItemTransfer
    {
        $billingItemEntity = new DstBillingItem();
        $billingItemEntity
            ->fromArray($billingItemTransfer->toArray());

        $billingItemEntity
            ->setFkBillingPeriod($billingItemTransfer->getBillingPeriod()->getIdBillingPeriod());

        $billingItemEntity->save();
        $billingItemTransfer->setIdBillingItem($billingItemEntity->getIdBillingItem());
        $this->saveTaxRateTotals($billingItemTransfer);

        return $this->entityToTransfer($billingItemEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\BillingItemTransfer $billingItemTransfer
     *
     * @return void
     */
    protected function saveTaxRateTotals(BillingItemTransfer $billingItemTransfer): void
    {
        foreach ($billingItemTransfer->getTaxRateTotals() as $taxRateTotal) {
            $taxRateTotalEntity = new DstBillingItemTaxRateTotal();
            $taxRateTotalEntity->setTaxRate($taxRateTotal->getRate());
            $taxRateTotalEntity->setTaxAmount($taxRateTotal->getAmount());
            $taxRateTotalEntity->setFkBillingItem($billingItemTransfer->getIdBillingItem());

            $taxRateTotalEntity->save();
        }
    }

    /**
     * @param \Orm\Zed\Billing\Persistence\DstBillingItem $entity
     *
     * @return \Generated\Shared\Transfer\BillingItemTransfer
     */
    protected function entityToTransfer(DstBillingItem $entity): BillingItemTransfer
    {
        $billingItemTransfer = (new BillingItemTransfer())
            ->fromArray($entity->toArray(), true)
            ->setBillingPeriod($this->getBillingPeriodById($entity->getFkBillingPeriod()));

        foreach ($entity->getDstBillingItemTaxRateTotals() as $taxRateTotalEntity) {
            $billingItemTransfer->addTaxRateTotals($this->taxRateTotalEntityToTransfer($taxRateTotalEntity));
        }

        return $billingItemTransfer;
    }

    /**
     * @param \Orm\Zed\Billing\Persistence\DstBillingItemTaxRateTotal $taxRateTotalEntity
     *
     * @return \Generated\Shared\Transfer\TaxRateTotalTransfer
     */
    protected function taxRateTotalEntityToTransfer(DstBillingItemTaxRateTotal $taxRateTotalEntity): TaxRateTotalTransfer
    {
        return (new TaxRateTotalTransfer())
            ->setAmount($taxRateTotalEntity->getTaxAmount())
            ->setRate($taxRateTotalEntity->getTaxRate());
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    protected function getBillingPeriodById(int $idBillingPeriod) : BillingPeriodTransfer
    {
        return $this
            ->billingPeriodModel
            ->getBillingPeriodById($idBillingPeriod);
    }
}
