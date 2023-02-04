<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-08
 * Time: 15:16
 */

namespace Pyz\Zed\Discount\Communication\Form\DataProvider;

use Orm\Zed\Merchant\Persistence\SpyBranch;
use Propel\Runtime\Collection\ObjectCollection;
use Pyz\Zed\Discount\Communication\Form\GeneralForm;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Discount\Communication\Form\DataProvider\DiscountFormDataProvider as SprykerDiscountFormDataProvider;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToCurrencyInterface;

class DiscountFormDataProvider extends SprykerDiscountFormDataProvider
{
    /**
     * @var MerchantQueryContainerInterface
     */
    protected $merchantQuery;

    /**
     * @var DiscountToCurrencyInterface
     */
    protected $currencyFacade;

    /**
     * DiscountFormDataProvider constructor.
     * @param MerchantQueryContainerInterface $merchantQuery
     * @param DiscountToCurrencyInterface $currencyFacade
     */
    public function __construct(MerchantQueryContainerInterface $merchantQuery, DiscountToCurrencyInterface $currencyFacade)
    {
        $this->merchantQuery = $merchantQuery;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @return array
     */
    protected function getBranchOptions(): array
    {
        $branchesArray = [];

        $branches = $this
            ->getAllNotDeletedBranches();

        if ($branches->isEmpty() !== true) {
            /* @var $branch SpyBranch */
            foreach ($branches as $branch) {
                $key = sprintf(
                    'ID: %s: %s (%s)',
                    $branch->getIdBranch(),
                    $branch->getName(),
                    $branch->getCode()
                );
                $branchesArray[$key] = $branch->getIdBranch();
            }
        }

        return $branchesArray;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $this->options[GeneralForm::OPTION_BRANCH_LIST] = $this
            ->getBranchOptions();

        return parent::getOptions();
    }

    /**
     * @return ObjectCollection
     */
    protected function getAllActiveBranches(): ObjectCollection
    {
        return $this
            ->merchantQuery
            ->queryBranchActive()
            ->find();
    }

    /**
     * @return ObjectCollection
     */
    protected function getAllNotDeletedBranches(): ObjectCollection
    {
        return $this
            ->merchantQuery
            ->queryBranchNotDeleted()
            ->find();
    }
}
