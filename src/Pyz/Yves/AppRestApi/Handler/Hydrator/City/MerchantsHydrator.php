<?php
/**
 * Durst - project - MerchantsHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-18
 * Time: 12:36
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\City;


use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\BranchKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\CityMerchantKeyResponseInterface;
use stdClass;

class MerchantsHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * MerchantsHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param AppRestApiConfig $config
     */
    public function __construct(
        AppRestApiClientInterface $client,
        AppRestApiConfig $config
    )
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $city = $responseObject
            ->{CityMerchantKeyResponseInterface::KEY_CITY};

        if ($city === null) {
            return;
        }

        $branches = $this
            ->getBranchesByZipCode(
                $requestObject->{Request::KEY_ZIP_CODE},
                $requestObject->{Request::KEY_BRANCH_CODE}
                );

        if ($branches->count() <= 0) {
            return;
        }

        $responseObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS} = [];

        foreach ($branches as $branch) {
            $responseObject
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS}[] = $this->hydrateMerchant($branch, $requestObject->{Request::KEY_ZIP_CODE});
        }
    }

    /**
     * @param string $zipCode
     * @param string $branchCode
     * @return ArrayObject
     */
    protected function getBranchesByZipCode(
        string $zipCode,
        string $branchCode
    ): ArrayObject
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setZipCode(
                $zipCode
            )
            ->setCode(
                $branchCode
            );

        $responseTransfer = $this
            ->getBranches(
                $requestTransfer
            );

        return $responseTransfer
            ->getBranches();
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @param string $zipCode
     * @return stdClass
     */
    protected function hydrateMerchant(BranchTransfer $branchTransfer, string $zipCode): stdClass
    {
        $merchantObject = new stdClass();

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_ID} = (string)$branchTransfer->getIdBranch();

        if ($branchTransfer->getLogoUrlLarge() !== null) {
            $merchantObject
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_LOGO} = sprintf(
                    '%s%s',
                    $this
                        ->config
                        ->getMerchantUploadPath(),
                    $branchTransfer
                        ->getLogoUrlLarge()
            );
        } else {
            $merchantObject
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_LOGO} = null;
        }

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_NAME} = $branchTransfer->getName();
        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_STREET} = sprintf(
                '%s %s',
                $branchTransfer
                    ->getStreet(),
                $branchTransfer
                    ->getNumber()
        );
        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_ZIP} = $branchTransfer->getZip();
        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_CITY} = $branchTransfer->getCity();
        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_PHONE} = $branchTransfer->getPhone();
        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_TERMS} = $branchTransfer->getTermsOfService();

        if ($branchTransfer->getHeidelpayPublicKey() === null || strlen(trim($branchTransfer->getHeidelpayPublicKey())) < 1) {
            $merchantObject
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY} = $this->config->getDurstHeidelpayPublicKey();
        } else {
            $merchantObject
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY} = $branchTransfer->getHeidelpayPublicKey();
        }

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_B2C_PAYMENT_METHODS} = [];
            foreach ($branchTransfer->getPaymentMethods() as $paymentMethod) {
                if($paymentMethod->getB2c() === true){
                    $merchantObject
                        ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_B2C_PAYMENT_METHODS}[] = $paymentMethod->getCode();
                }
            }

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_B2B_PAYMENT_METHODS} = [];
            foreach ($branchTransfer->getPaymentMethods() as $paymentMethod) {
                if($paymentMethod->getB2b() === true){
                    $merchantObject
                        ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_B2B_PAYMENT_METHODS}[] = $paymentMethod->getCode();
                }
            }

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANT_IS_WHOLESALE} = $this->isBranchWholesale($branchTransfer);

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_COMMENTS_ENABLED} = $this->getCommentsEnabled($branchTransfer);

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BRANCH_CODE} = $branchTransfer->getCode();

        $merchantObject
            ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING} = $this->getBillingInformation($branchTransfer);

        $merchantObject->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_OFFERS_DEPOSIT_PICKUP} = ($branchTransfer->getOffersDepositPickup() === true);

        $merchantObject->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_GRAPHMASTER_SETTINGS} = $this->getGMSettings($branchTransfer);

        return $merchantObject;
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return bool
     */
    protected function getCommentsEnabled(BranchTransfer $branchTransfer): bool
    {
        foreach ($branchTransfer->getSoftwareFeatures() as $softwareFeature) {
            if ($softwareFeature->getCode() === $this->config->getAllowCommentsSoftwareFeatureCode()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return bool
     */
    protected function isBranchWholesale(BranchTransfer $branchTransfer): bool
    {
        return (
            $branchTransfer->getCodeSoftwarePackage() === SoftwarePackageConstants::SOFTWARE_PACKAGE_WHOLESALE_CODE
        );
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return stdClass
     */
    protected function getBillingInformation(BranchTransfer $branchTransfer): stdClass
    {
        $billing = new stdClass();

        if ($branchTransfer->getBillingCompany() !== null) {
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_COMPANY} = $branchTransfer->getBillingCompany();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_STREET} = $branchTransfer->getBillingStreet();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_STREET_NUMBER} = $branchTransfer->getBillingNumber();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_ZIP_CODE} = $branchTransfer->getBillingZip();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_CITY} = $branchTransfer->getBillingCity();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_SALES_TAX_ID} = $branchTransfer->getSalesTaxId();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_PLACE_JURISDICTION} = $branchTransfer->getPlaceJurisdiction();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_ECO_CONTROL_NUMBER} = $branchTransfer->getEcoControlNumber();
            $billing
                ->{CityMerchantKeyResponseInterface::KEY_MERCHANTS_BILLING_PERSON_RESPONSIBLE} = $branchTransfer->getPersonResponsible();
        }

        return $billing;
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    protected function getBranches(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        if (empty($requestTransfer->getZipCode()) !== true) {
            return $this
                ->getByZipCode(
                    $requestTransfer
                );
        }

        return $this
            ->getByBranchCode(
                $requestTransfer
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    protected function getByZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->client
            ->getBranchesByZipCode(
                $requestTransfer
            );
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    protected function getByBranchCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $response = $this
            ->client
            ->getBranchByCode(
                $requestTransfer
            );

        $branch = $response
            ->getBranch();

        $responseTransfer = new AppApiResponseTransfer();

        $responseTransfer
            ->addBranches($branch);

        return $responseTransfer;
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @return array|null
     */
    protected function getGMSettings(BranchTransfer $branchTransfer) : ?array
    {
        if($branchTransfer->getUsesGraphmasters() != true){
            return null;
        }

        // todo what happens if no settings are found
        $settings = $this
            ->client
            ->getGMSettings($branchTransfer->getIdBranch());

        return $settings;
    }
}
