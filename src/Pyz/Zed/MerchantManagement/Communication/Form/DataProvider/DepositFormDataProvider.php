<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 25.10.17
 * Time: 13:06
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;


use Orm\Zed\Deposit\Persistence\Map\SpyDepositTableMap;
use Pyz\Zed\Deposit\Business\DepositQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Form\DepositForm;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class DepositFormDataProvider
{
    /**
     * @var DepositQueryContainerInterface
     */
    protected $depositFacade;

    /**
     * @var MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * DepositFormDataProvider constructor.
     * @param DepositQueryContainerInterface $depositFacade
     * @param MoneyFacadeInterface $moneyFacade
     */
    public function __construct(DepositQueryContainerInterface $depositFacade, MoneyFacadeInterface $moneyFacade)
    {
        $this->depositFacade = $depositFacade;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param int $idDeposit
     * @return array
     */
    public function getData($idDeposit)
    {
        $depositTransfer = $this->depositFacade->getDepositById($idDeposit);

        $formData = $depositTransfer->toArray();
        $formData[DepositForm::FIELD_PRICE] = $this->formatPrice($depositTransfer->getPrice());

        return $formData;

    }

    /**
     * @param int $price
     * @return double
     */
    protected function formatPrice($price)
    {
        if($price === null)
            return '';

        return $this->moneyFacade->convertIntegerToDecimal($price);
    }

}