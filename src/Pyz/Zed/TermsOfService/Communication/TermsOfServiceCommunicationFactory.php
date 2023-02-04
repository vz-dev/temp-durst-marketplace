<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 15.01.18
 * Time: 17:12
 */

namespace Pyz\Zed\TermsOfService\Communication;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Pyz\Zed\TermsOfService\Communication\Form\TermsOfServiceForm;
use Pyz\Zed\TermsOfService\TermsOfServiceConfig;
use Pyz\Zed\TermsOfService\TermsOfServiceDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * Class TermsOfServiceCommunicationFactory
 * @package Pyz\Zed\TermsOfService\Communication
 * @method TermsOfServiceFacadeInterface getFacade()
 * @method TermsOfServiceConfig getConfig()
 */
class TermsOfServiceCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(TermsOfServiceDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createTermsOfServiceForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(TermsOfServiceForm::class, $data, $options);
    }
}
