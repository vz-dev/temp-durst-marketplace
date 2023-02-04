<?php
/**
 * Durst - project - MerchantsHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 16:19
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Branch;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\BranchKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\BranchKeyResponseInterface as Response;
use stdClass;

class MerchantsHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * MerchantsHydrator constructor.
     *
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $client
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     */
    public function __construct(AppRestApiClientInterface $client, AppRestApiConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return mixed|void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        if ($requestObject->{Request::KEY_MERCHANT_ID} > 0) {
            $branches = $this->getBranchByIdAndZipCode(
                $requestObject->{Request::KEY_MERCHANT_ID},
                $requestObject->{Request::KEY_ZIP_CODE}
            );
        } else {
            $branches = $this->getBranchesByZipCode($requestObject->{Request::KEY_ZIP_CODE});
        }

        if ($branches->count() <= 0) {
            $responseObject->{Response::KEY_ZIP_CODE_MERCHANTS_FOUND} = false;
            return;
        }

        $responseObject->{Response::KEY_ZIP_CODE_MERCHANTS_FOUND} = true;
        $responseObject->{Response::KEY_MERCHANTS} = [];

        foreach ($branches as $branch) {
            $responseObject->{Response::KEY_MERCHANTS}[] = $this->hydrateMerchant($branch);
        }
    }

    /**
     * @param int $idBranch
     * @param string $zipCode
     *
     * @return \ArrayObject
     */
    protected function getBranchByIdAndZipCode(int $idBranch, string $zipCode) : ArrayObject
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($idBranch)
            ->setZipCode($zipCode);

        $responseTransfer = $this
            ->client
            ->getBranchByIdAndZipCode($requestTransfer);

        if ($responseTransfer->getBranch() === null) {
            return new ArrayObject();
        }

        return new ArrayObject([
            $responseTransfer->getBranch(),
        ]);
    }

    /**
     * @param string $zipCode
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\BranchTransfer[]
     */
    protected function getBranchesByZipCode(string $zipCode) : ArrayObject
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setZipCode($zipCode);

        $responseTransfer = $this
            ->client
            ->getBranchesByZipCode($requestTransfer);

        return $responseTransfer->getBranches();
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \stdClass
     */
    protected function hydrateMerchant(BranchTransfer $branchTransfer) : stdClass
    {
        $merchantObject = new stdClass();

        $merchantObject->{Response::KEY_MERCHANTS_ID} = $branchTransfer->getIdBranch();

        if ($branchTransfer->getLogoUrlLarge()) {
            $merchantObject->{Response::KEY_MERCHANTS_LOGO} = sprintf(
                '%s%s',
                $this->config->getMerchantUploadPath(),
                $branchTransfer->getLogoUrlLarge()
            );
        } else {
            $merchantObject->{Response::KEY_MERCHANTS_LOGO} = null;
        }

        $merchantObject->{Response::KEY_MERCHANTS_NAME} = $branchTransfer->getName();
        $merchantObject->{Response::KEY_MERCHANTS_STREET} = sprintf(
            '%s %s',
            $branchTransfer->getStreet(),
            $branchTransfer->getNumber()
        );
        $merchantObject->{Response::KEY_MERCHANTS_ZIP} = $branchTransfer->getZip();
        $merchantObject->{Response::KEY_MERCHANTS_CITY} = $branchTransfer->getCity();
        $merchantObject->{Response::KEY_MERCHANTS_PHONE} = $branchTransfer->getPhone();
        $merchantObject->{Response::KEY_MERCHANTS_TERMS} = $branchTransfer->getTermsOfService();
        if ($branchTransfer->getHeidelpayPublicKey() === null || strlen(trim($branchTransfer->getHeidelpayPublicKey())) < 1) {
            $merchantObject
                ->{Response::KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY} = $this->config->getDurstHeidelpayPublicKey();
        } else {
            $merchantObject
                ->{Response::KEY_MERCHANTS_HEIDELPAY_PUBLIC_KEY} = $branchTransfer->getHeidelpayPublicKey();
        }
        $merchantObject
            ->{Response::KEY_MERCHANTS_B2C_PAYMENT_METHODS} = [];
        foreach ($branchTransfer->getPaymentMethods() as $paymentMethod) {
            if ($paymentMethod->getB2c() === true) {
                $merchantObject
                    ->{Response::KEY_MERCHANTS_B2C_PAYMENT_METHODS}[] = $paymentMethod->getCode();
            }
        }
        $merchantObject
            ->{Response::KEY_MERCHANTS_B2B_PAYMENT_METHODS} = [];
        foreach ($branchTransfer->getPaymentMethods() as $paymentMethod) {
            if ($paymentMethod->getB2b() === true) {
                $merchantObject
                    ->{Response::KEY_MERCHANTS_B2B_PAYMENT_METHODS}[] = $paymentMethod->getCode();
            }
        }
        $merchantObject->{Response::KEY_MERCHANTS_COMMENTS_ENABLED} = $this->getCommentsEnabled($branchTransfer);

        $merchantObject
            ->{Response::KEY_MERCHANT_IS_WHOLESALE} = $this->isBranchWholesale($branchTransfer);

        return $merchantObject;
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return bool
     */
    protected function getCommentsEnabled(BranchTransfer $branchTransfer) : bool
    {
        foreach ($branchTransfer->getSoftwareFeatures() as $softwareFeature) {
            if ($softwareFeature->getCode() === $this->config->getAllowCommentsSoftwareFeatureCode()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return bool
     */
    protected function isBranchWholesale(BranchTransfer $branchTransfer): bool
    {
        return (
            $branchTransfer->getCodeSoftwarePackage() === SoftwarePackageConstants::SOFTWARE_PACKAGE_WHOLESALE_CODE
        );
    }
}
