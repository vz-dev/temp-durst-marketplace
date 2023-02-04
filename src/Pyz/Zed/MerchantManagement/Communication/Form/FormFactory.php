<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 26.10.17
 * Time: 10:16
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\BranchUserTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Pyz\Zed\MerchantManagement\Communication\AbstractMerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\BranchFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\BranchUserFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DeliveryAreaFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DepositFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantUpdateFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantUserFormDataProvider;
use Symfony\Component\Form\FormInterface;

class FormFactory extends AbstractMerchantManagementCommunicationFactory
{
    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createDepositForm(array $data = [], array $options = [])
    {
        return $this
            ->getFormFactory()
            ->create(DepositForm::class, $data, $options);
    }

    /**
     * @param MerchantTransfer $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createMerchantForm(MerchantTransfer $data, array $options = [])
    {
        return $this
            ->getFormFactory()
            ->create(MerchantForm::class, $data, $options);
    }

    /**
     * @param MerchantTransfer $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUpdateMerchantForm(MerchantTransfer $data, array $options = [])
    {
        return $this
            ->getFormFactory()
            ->create(MerchantUpdateForm::class, $data, $options);
    }

    /**
     * @return MerchantFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantFormDataProvider() : MerchantFormDataProvider
    {
        return new MerchantFormDataProvider(
            $this->getMerchantFacade(),
            $this->getSoftwarePackageQueryContainer(),
            $this->getAclFacade()
        );
    }

    /**
     * @return MerchantUpdateFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantUpdateFormDataProvider() : MerchantUpdateFormDataProvider
    {
        return new MerchantUpdateFormDataProvider(
            $this->getMerchantFacade(),
            $this->getSoftwarePackageQueryContainer(),
            $this->getAclFacade()
        );
    }

    /**
     * @return DepositFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDepositFormDataProvider()
    {
        return new DepositFormDataProvider($this->getDepositFacade(), $this->getMoneyFacade());
    }

    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createDeliveryAreaForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(DeliveryAreaForm::class, $data, $options);
    }

    /**
     * @return DeliveryAreaFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDeliveryAreaFormDataProvider()
    {
        return new DeliveryAreaFormDataProvider($this->getDeliveryAreaFacade());
    }

    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createPaymentMethodForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(PaymentMethodForm::class, $data, $options);
    }

    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createSalutationForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create(SalutationForm::class, $data, $options);
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

    /**
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createBranchForm(array $options = [])
    {
        $data = new BranchTransfer();

        return $this
            ->getFormFactory()
            ->create(BranchForm::class, $data, $options);
    }

    /**
     * @return BranchFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchFormDataProvider()
    {
        return new BranchFormDataProvider(
            $this->getMerchantQueryContainer(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @param BranchTransfer $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createBranchUpdateForm(BranchTransfer $data, array $options = [])
    {
        return $this
            ->getFormFactory()
            ->create(BranchUpdateForm::class, $data, $options);
    }

    /**
     * @return \Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\BranchUserFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchUserFormDataProvider(): BranchUserFormDataProvider
    {
        return new BranchUserFormDataProvider(
            $this->getMerchantQueryContainer(),
            $this->getMerchantFacade(),
            $this->getAclFacade()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\BranchUserTransfer|null $branchUserTransfer
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createBranchUserForm(
        ?BranchUserTransfer $branchUserTransfer,
        array $options = []
    ): FormInterface
    {
        if ($branchUserTransfer === null) {
            $branchUserTransfer = new BranchUserTransfer();
        }

        return $this
            ->getFormFactory()
            ->create(
                BranchUserUpdateForm::class,
                $branchUserTransfer,
                $options
            );
    }

    /**
     * @return \Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantUserFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantUserFormDataProvider(): MerchantUserFormDataProvider
    {
        return new MerchantUserFormDataProvider(
            $this->getMerchantQueryContainer(),
            $this->getMerchantFacade(),
            $this->getAclFacade()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUserTransfer|null $merchantUserTransfer
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createMerchantUserForm(
        ?MerchantUserTransfer $merchantUserTransfer,
        array $options = []
    ): FormInterface
    {
        if ($merchantUserTransfer === null) {
            $merchantUserTransfer = new MerchantUserTransfer();
        }

        return $this
            ->getFormFactory()
            ->create(
                MerchantUserUpdateForm::class,
                $merchantUserTransfer,
                $options
            );
    }
}
