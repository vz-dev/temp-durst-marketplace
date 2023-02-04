<?php
/**
 * Durst - project - SoftwarePackageFormDataProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 21:24
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Form\DataProvider;

use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\SoftwarePackage\Communication\Form\SoftwarePackageForm;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;

class SoftwarePackageFormDataProvider
{
    /**
     * @var SoftwarePackageFacadeInterface
     */
    protected $facade;

    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantQueryContainer;

    /**
     * SoftwarePackageFormDataProvider constructor.
     * @param SoftwarePackageFacadeInterface $facade
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     */
    public function __construct(
        SoftwarePackageFacadeInterface $facade,
        SoftwarePackageQueryContainerInterface $queryContainer,
        MerchantQueryContainerInterface $merchantQueryContainer
    )
    {
        $this->facade = $facade;
        $this->queryContainer = $queryContainer;
        $this->merchantQueryContainer = $merchantQueryContainer;
    }

    /**
     * @param int $idSoftwarePackageForm
     * @return SoftwarePackageTransfer
     */
    public function getData(int $idSoftwarePackageForm = null): SoftwarePackageTransfer
    {
        if ($idSoftwarePackageForm === null) {
            return new SoftwarePackageTransfer();
        }

        return $this
            ->facade
            ->getSoftwarePackageById($idSoftwarePackageForm);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            SoftwarePackageForm::OPTION_PAYMENT_METHOD_CHOICES => $this->getPaymentMethodSelectChoices(),
            SoftwarePackageForm::OPTION_SOFTWARE_FEATURE_CHOICES => $this->getSoftwareFeatureSelectChoices()
        ];
    }

    /**
     * @return array
     */
    protected function getPaymentMethodSelectChoices(): array
    {
        $paymentMethods = $this
            ->merchantQueryContainer
            ->queryPaymentMethod()
            ->find();

        $options = [];
        foreach ($paymentMethods as $paymentMethod) {
            $options[$paymentMethod->getCode()] = $paymentMethod->getIdPaymentMethod();
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getSoftwareFeatureSelectChoices(): array
    {
        $softwareFeatures = $this
            ->queryContainer
            ->querySoftwareFeature()
            ->find();

        $options = [];
        foreach ($softwareFeatures as $softwareFeature){
            $options[$softwareFeature->getCode()] = $softwareFeature->getIdSoftwareFeature();
        }

        return $options;
    }

}
