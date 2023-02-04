<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DepositPickup;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DepositPickupCreateInquiryKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DepositPickupCreateInquiryKeyResponseInterface;
use stdClass;

class DepositPickupCreateInquiryHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @param AppRestApiClientInterface $client
     */
    public function __construct(AppRestApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
         $requestTransfer = (new AppApiRequestTransfer())
            ->setDepositPickupInquiry(
                (new DepositPickupInquiryTransfer())
                    ->setFkBranch($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_BRANCH_ID})
                    ->setName($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_NAME})
                    ->setAddress($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_ADDRESS})
                    ->setEmail($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_EMAIL})
                    ->setPhoneNumber($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_PHONE_NUMBER})
                    ->setPreferredDate($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_PREFERRED_DATE})
                    ->setMessage($requestObject->{DepositPickupCreateInquiryKeyRequestInterface::KEY_MESSAGE})
            );

        $responseTransfer = $this
            ->client
            ->createDepositPickupInquiry($requestTransfer);

        if ($responseTransfer->getError() !== null) {
            $errorObject = new stdClass();
            $errorObject->{DepositPickupCreateInquiryKeyResponseInterface::KEY_ERROR_CODE} = $responseTransfer->getError()->getCode();
            $errorObject->{DepositPickupCreateInquiryKeyResponseInterface::KEY_ERROR_MESSAGE} = $responseTransfer->getError()->getMessage();

            $responseObject->{DepositPickupCreateInquiryKeyResponseInterface::KEY_ERROR} = $errorObject;
        }

        $responseObject->{DepositPickupCreateInquiryKeyResponseInterface::KEY_IS_SUCCESS} = $responseTransfer->getIsSuccess();
    }
}
