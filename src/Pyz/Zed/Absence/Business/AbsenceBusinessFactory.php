<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 16:15
     */

namespace Pyz\Zed\Absence\Business;


use Pyz\Zed\Absence\AbsenceDependencyProvider;
use Pyz\Zed\Absence\Business\Model\Absence;
use Pyz\Zed\Absence\Persistence\AbsenceQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class AbsenceBusinessFactory
 * @package Pyz\Zed\Absence\Business
 * @method AbsenceQueryContainerInterface getQueryContainer()
 */
class AbsenceBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return Absence
     * @throws ContainerKeyNotFoundException
     */
    public function createAbsenceModel()
    {
        return new Absence(
            $this->getQueryContainer(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(AbsenceDependencyProvider::FACADE_MERCHANT);
    }
}
