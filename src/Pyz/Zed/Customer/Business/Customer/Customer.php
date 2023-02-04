<?php
/**
 * Durst - project - Customer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 10:45
 */

namespace Pyz\Zed\Customer\Business\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Customer\Communication\Plugin\Mail\CustomerRegistrationMailTypePlugin;
use Pyz\Zed\Customer\CustomerConfig;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Customer\Business\Customer\Customer as SprykerCustomer;
use Spryker\Zed\Customer\Business\Customer\EmailValidatorInterface;
use Spryker\Zed\Customer\Business\CustomerExpander\CustomerExpanderInterface;
use Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGeneratorInterface;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToMailInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;
use Spryker\Zed\Locale\Persistence\LocaleQueryContainerInterface;

class Customer extends SprykerCustomer
{
    /**
     * @var CustomerConfig
     */
    protected $customerConfig;

    /**
     * @var \Pyz\Zed\Oms\Business\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @param \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGeneratorInterface $customerReferenceGenerator
     * @param \Pyz\Zed\Customer\CustomerConfig $customerConfig
     * @param \Spryker\Zed\Customer\Business\Customer\EmailValidatorInterface $emailValidator
     * @param \Spryker\Zed\Customer\Dependency\Facade\CustomerToMailInterface $mailFacade
     * @param \Spryker\Zed\Locale\Persistence\LocaleQueryContainerInterface $localeQueryContainer
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \Spryker\Zed\Customer\Business\CustomerExpander\CustomerExpanderInterface $customerExpander
     * @param array $postCustomerRegistrationPlugins
     * @param \Pyz\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct(
        CustomerQueryContainerInterface $queryContainer,
        CustomerReferenceGeneratorInterface $customerReferenceGenerator,
        CustomerConfig $customerConfig,
        EmailValidatorInterface $emailValidator,
        CustomerToMailInterface $mailFacade,
        LocaleQueryContainerInterface $localeQueryContainer,
        Store $store,
        CustomerExpanderInterface $customerExpander,
        array $postCustomerRegistrationPlugins = [],
        OmsFacadeInterface $omsFacade
    )
    {
        $this->omsFacade = $omsFacade;

        parent::__construct(
            $queryContainer,
            $customerReferenceGenerator,
            $customerConfig,
            $emailValidator,
            $mailFacade,
            $localeQueryContainer,
            $store,
            $customerExpander,
            $postCustomerRegistrationPlugins
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     * @return bool
     */
    protected function sendRegistrationToken(
        CustomerTransfer $customerTransfer
    ): bool
    {
        $confirmationLink = $this
            ->customerConfig
            ->getRegisterConfirmTokenUrl(
                $customerTransfer
                    ->getRegistrationKey()
            );

        $customerTransfer
            ->setConfirmationLink(
                $confirmationLink
            );

        $mailTransfer = new MailTransfer();

        $mailTransfer
            ->setType(
                CustomerRegistrationMailTypePlugin::MAIL_TYPE
            )
            ->setCustomer(
                $customerTransfer
            )
            ->setLocale(
                $customerTransfer
                    ->getLocale()
            )
            ->setBaseUrl(
                $this
                    ->customerConfig
                    ->getBaseUrl()
            )
            ->setFooterBannerImg(
                $this
                    ->customerConfig
                    ->getFooterBannerImg()
            )
            ->setFooterBannerLink(
                $this
                    ->customerConfig
                    ->getFooterBannerLink()
            )
            ->setFooterBannerAlt(
                $this
                    ->customerConfig
                    ->getFooterBannerAlt()
            )
            ->setFooterBannerCta(
                $this
                    ->customerConfig
                    ->getFooterBannerCta()
            )
            ->setDurst(
                $this
                    ->omsFacade
                    ->createDurstCompanyTransfer()
            );

        $this
            ->mailFacade
            ->handleMail(
                $mailTransfer
            );

        return true;
    }
}
