<?php
/**
 * Durst - project - DepositRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 17:14
 */

namespace Pyz\Zed\Integra\Business\Model\Quote\Deposit;

use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class DepositRepository implements DepositRepositoryInterface
{
    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var SpyDeposit[]
     */
    protected $deposits = [];

    /**
     * DepositRepository constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $sku
     *
     * @return SpyDeposit
     */
    public function getDepositForSku(string $sku): SpyDeposit
    {
        if (array_key_exists($sku, $this->deposits) !== true) {
            //throw EntityNotFoundException::deposit($sku);


        }

        return $this->deposits[$sku];
    }

    /**
     * @param array $skus
     *
     * @return void
     */
    public function loadDeposits(
        array $skus
    ): void {
        $entities = $this
            ->queryContainer
            ->queryDepositForSkus($skus)
            ->find();

        foreach ($entities as $entity) {
            foreach ($entity->getSpyProducts() as $product) {
                $this->deposits[$product->getSku()] = $entity;
            }
        }
    }
}
