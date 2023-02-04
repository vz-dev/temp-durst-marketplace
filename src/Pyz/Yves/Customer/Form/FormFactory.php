<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */
namespace Pyz\Yves\Customer\Form;

use Pyz\Yves\Customer\CustomerDependencyProvider;
use Pyz\Yves\Customer\Form\DataProvider\AddressFormDataProvider;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;

class FormFactory extends AbstractFactory
{
    /**
     * @return \Symfony\Component\Form\FormFactory
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormFactory()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @param array $formOptions
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAddressForm(array $formOptions = [])
    {
        $addressFormType = AddressForm::class;

        return $this->getFormFactory()->create($addressFormType, null, $formOptions);
    }

    /**
     * @return AddressFormDataProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAddressFormDataProvider()
    {
        return new AddressFormDataProvider($this->getCustomerClient(), $this->getStore());
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRegisterForm()
    {
        $registerFormType = RegisterForm::class;

        return $this->getFormFactory()->create($registerFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createLoginForm()
    {
        $loginFormType = LoginForm::class;

        return $this->getFormFactory()->create($loginFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createForgottenPasswordForm()
    {
        $forgottenPasswordFormType = ForgottenPasswordForm::class;

        return $this->getFormFactory()->create($forgottenPasswordFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProfileForm()
    {
        $profileFormType = ProfileForm::class;

        return $this->getFormFactory()->create($profileFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createFormRestorePassword()
    {
        $restorePasswordFormType = RestorePasswordForm::class;

        return $this->getFormFactory()->create($restorePasswordFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPasswordForm()
    {
        $passwordFormType = PasswordForm::class;

        return $this->getFormFactory()->create($passwordFormType);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createNewsletterSubscriptionForm()
    {
        $newsletterSubscriptionFormType = NewsletterSubscriptionForm::class;

        return $this->getFormFactory()->create($newsletterSubscriptionFormType);
    }

    /**
     * @return \Pyz\Client\Customer\CustomerClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCustomerClient()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getStore()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::STORE);
    }
}
