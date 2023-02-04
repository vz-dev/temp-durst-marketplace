<?php
/**
 * Durst - project - TermsHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.05.18
 * Time: 09:10
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot;

use Pyz\Client\TermsOfService\TermsOfServiceClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\TimeSlotKeyResponseInterface as Response;
use stdClass;

class TermsHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\TermsOfService\TermsOfServiceClientInterface
     */
    protected $termsOfServiceClient;

    /**
     * TermsHydrator constructor.
     *
     * @param \Pyz\Client\TermsOfService\TermsOfServiceClientInterface $termsOfServiceClient
     */
    public function __construct(TermsOfServiceClientInterface $termsOfServiceClient)
    {
        $this->termsOfServiceClient = $termsOfServiceClient;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $this->hydrateDurstTerms($responseObject);
    }

    /**
     * @param \stdClass $responseObjec
     *
     * @return void
     */
    protected function hydrateDurstTerms(stdClass $responseObject)
    {
        $customerTerms = $this
            ->termsOfServiceClient
            ->getCustomerTerms();

        $durstTerms = $this->createStdClass();
        $durstTerms->{Response::KEY_DURST_TERMS_BUTTON_TEXT} = $customerTerms->getButtonText();
        $durstTerms->{Response::KEY_DURST_TERMS_HINT_TEXT} = $customerTerms->getHintText();
        $durstTerms->{Response::KEY_DURST_TERMS_TEXT} = $customerTerms->getText();

        $responseObject->{Response::KEY_DURST_TERMS} = $durstTerms;
    }

    /**
     * @return \stdClass
     */
    protected function createStdClass(): stdClass
    {
        return new stdClass();
    }
}
