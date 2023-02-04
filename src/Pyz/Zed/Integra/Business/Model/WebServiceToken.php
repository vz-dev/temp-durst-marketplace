<?php
/**
 * Durst - project - WebServiceToken.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-09
 * Time: 11:40
 */

namespace Pyz\Zed\Integra\Business\Model;

use Generated\Shared\Transfer\IntegraWebserviceTokenTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Orm\Zed\Integra\Persistence\DstIntegraWebServiceToken;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class WebServiceToken implements WebServiceTokenInterface
{
    protected const RESPONSE_AUTH_TOKEN_KEY = 'AuthToken';
    protected const REPONSE_AUTH_TOKEN_SESSION_ID_KEY = 'SessionID';

    protected const INTEGRA_WSDL_LOGIN_FUNCTION = 'RequestLogin';

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var IntegraConfig
     */
    protected $config;

    /**
     * @var SoapRequestServiceInterface
     */
    protected $soapRequestService;

    public function __construct(
        IntegraQueryContainerInterface $queryContainer,
        IntegraConfig $config,
        SoapRequestServiceInterface $soapRequestService
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->soapRequestService = $soapRequestService;
    }

    /**
     * @param int $idBranch
     *
     * @return IntegraWebserviceTokenTransfer
     */
    public function getCurrentTokenForBranch(int $idBranch): IntegraWebserviceTokenTransfer
    {
        $token = $this
            ->queryContainer
            ->queryWebserviceTokenForBranch($idBranch)
            ->filterByIsValid(true)
            ->orderByCreatedAt(Criteria::DESC)
            ->findOne();

        if ($token === null) {
            $token = $this->getNewTokenForBranch($idBranch);
        }

        return $this->getTransferFromEntity($token);
    }

    /**
     * @param int $idBranch
     *
     * @return IntegraWebserviceTokenTransfer
     */
    public function createTokenForBranch(int $idBranch) : IntegraWebserviceTokenTransfer
    {
        $token = $this->getNewTokenForBranch($idBranch);

        return $this->getTransferFromEntity($token);
    }

    /**
     * @param IntegraWebserviceTokenTransfer $tokenTransfer
     */
    protected function saveTokenForBranch(IntegraWebserviceTokenTransfer $tokenTransfer) : DstIntegraWebServiceToken
    {
        $this->deactivateOldTokensForBranch($tokenTransfer->getIdBranch());

        $tokenEntity = new DstIntegraWebServiceToken();
        $tokenEntity
            ->setFkBranch($tokenTransfer->getIdBranch())
            ->setToken($tokenTransfer->getToken())
            ->setIsValid(true)
            ->save();

        return $tokenEntity;
    }

    /**
     * @param int $idBranch
     *
     * @return void
     */
    protected function deactivateOldTokensForBranch(int $idBranch)
    {
        $oldTokens = $this
            ->queryContainer
            ->queryWebserviceToken()
            ->filterByFkBranch($idBranch)
            ->filterByIsValid(true)
            ->find();

        foreach ($oldTokens as $oldToken) {
            $oldToken
                ->setIsValid(false)
                ->save();
        }
    }

    /**
     * @param DstIntegraWebServiceToken $dstIntegraWebServiceToken
     *
     * @return IntegraWebserviceTokenTransfer
     */
    protected function getTransferFromEntity(DstIntegraWebServiceToken $dstIntegraWebServiceToken) : IntegraWebserviceTokenTransfer
    {
        return (new IntegraWebserviceTokenTransfer())
            ->fromArray($dstIntegraWebServiceToken->toArray(), true)
            ->setIdBranch($dstIntegraWebServiceToken->getFkBranch());
    }

    /**
     * @param int $idBranch
     *
     * @return DstIntegraWebServiceToken
     */
    protected function getNewTokenForBranch(int $idBranch) : DstIntegraWebServiceToken
    {
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createSoapRequestTransfer());

        $tokenTransfer = (new IntegraWebserviceTokenTransfer())
            ->setIdBranch($idBranch)
            ->setToken($response->getData()[self::RESPONSE_AUTH_TOKEN_KEY][self::REPONSE_AUTH_TOKEN_SESSION_ID_KEY]);

        return $this
            ->saveTokenForBranch($tokenTransfer);
    }

    /**
     * @return SoapRequestTransfer
     */
    protected function createSoapRequestTransfer() : SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService($this->config->getIntegraWebserviceSessionKey())
            ->setFunction(self::INTEGRA_WSDL_LOGIN_FUNCTION)
            ->setArgs($this->getLoginParamsArray());
    }

    /**
     * @todo actually get Login params from DB so that they are dependent on branch
     *
     * @return array
     */
    protected function getLoginParamsArray() : array
    {
        return [
            'User' => $this->config->getGbzIntegraUser(),
            'Password' => $this->config->getGbzIntegraPassword(),
            'Mandat' => $this->config->getGbzIntegraMandat(),
            'BetrSt' => $this->config->getGbzIntegraBetrSt(),
        ];
    }
}
