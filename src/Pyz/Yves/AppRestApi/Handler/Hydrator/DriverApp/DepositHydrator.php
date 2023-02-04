<?php


namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp;

use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\DepositMerchantConnector\DepositMerchantConnectorClientInterface;
use Pyz\Client\DriverApp\DriverAppClientInterface;
use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverDepositRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverDepositResponseInterface as Response;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class DepositHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\DepositMerchantConnector\DepositMerchantConnectorClientInterface
     */
    protected $depositMerchantConnectorClient;

    /**
     * @var \Spryker\Yves\Money\Plugin\MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * @var \Pyz\Client\Merchant\MerchantClientInterface
     */
    protected $merchantClient;

    /**
     * @var \Pyz\Client\Auth\AuthClientInterface
     */
    protected $authClient;

    /**
     * @var \Pyz\Client\DriverApp\DriverAppClientInterface
     */
    protected $driverAppClient;

    /**
     * DepositHydrator constructor.
     *
     * @param \Pyz\Client\DepositMerchantConnector\DepositMerchantConnectorClientInterface $depositMerchantConnectorClient
     * @param \Spryker\Yves\Money\Plugin\MoneyPlugin $moneyPlugin
     * @param \Pyz\Client\Merchant\MerchantClientInterface $merchantClient
     * @param \Pyz\Client\Auth\AuthClientInterface $authClient
     * @param \Pyz\Client\DriverApp\DriverAppClientInterface $driverAppClient
     */
    public function __construct(
        DepositMerchantConnectorClientInterface $depositMerchantConnectorClient,
        MoneyPlugin $moneyPlugin,
        MerchantClientInterface $merchantClient,
        AuthClientInterface $authClient,
        DriverAppClientInterface $driverAppClient
    ) {
        $this->depositMerchantConnectorClient = $depositMerchantConnectorClient;
        $this->moneyPlugin = $moneyPlugin;
        $this->merchantClient = $merchantClient;
        $this->authClient = $authClient;
        $this->driverAppClient = $driverAppClient;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $authenticated = $this
            ->authenticateDriver($requestObject);

        $responseObject->{Response::KEY_AUTH_VALID} = $authenticated;


        if ($authenticated !== true) {
            $responseObject->{Response::KEY_DEPOSIT_ENTRIES} = [];
            return;
        }

        $this->checkVersion($requestObject, $responseObject);

        $token = $this
            ->getToken($requestObject);

        $driver = $this
            ->getDriverByToken($token);

        $branchTransfer = $this
            ->merchantClient
            ->getBranchById($driver->getFkBranch());

        $deposits = $this
            ->depositMerchantConnectorClient
            ->getDepositsForBranch($branchTransfer);

        $responseObject->{Response::KEY_DEPOSIT_ENTRIES} = $this->hydrateDeposits($deposits);
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return bool
     */
    protected function authenticateDriver(stdClass $requestObject): bool
    {
        $token = $requestObject
            ->{Request::KEY_TOKEN};
        if ($token == null || trim($token) == '') {
            return false;
        }

        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($token);

        $response = $this
            ->authClient
            ->authenticateDriver($requestTransfer);

        return $response
            ->getAuthValid();
    }

    protected function checkVersion(
        stdClass $requestObject,
        stdClass $responseObject
    ): void
    {
        $responseObject->{Response::KEY_IS_UPDATABLE} = false;
        if(isset($requestObject->{Request::KEY_CURRENT_VERSION}) && $requestObject->{Request::KEY_CURRENT_VERSION} !== null){
            $responseObject->{Response::KEY_IS_UPDATABLE} = $this
                ->driverAppClient
                ->isUpdatable($requestObject->{Request::KEY_CURRENT_VERSION});
        }
    }

    /**
     * @param \stdClass $requestObject
     *
     * @return string
     */
    protected function getToken(stdClass $requestObject): string
    {
        return $requestObject
            ->{Request::KEY_TOKEN};
    }

    /**
     * @param string $token
     *
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function getDriverByToken(string $token): DriverTransfer
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setToken($token);

        return $this
            ->authClient
            ->getDriverByToken($requestTransfer);
    }

    /**
     * @param iterable $deposits
     *
     * @return array
     */
    protected function hydrateDeposits(iterable $deposits): array
    {
        $responseDeposits = [];

        /** @var \Generated\Shared\Transfer\DepositTransfer $deposit */
        foreach ($deposits as $deposit) {
            $currentDeposit = new stdClass();

            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_ID} = $deposit->getIdDeposit();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_NAME} = $deposit->getName();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT} = $this->formatMoney($deposit->getDeposit());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT_B2B} = $this->formatMoney($deposit->getDepositB2b());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_CODE} = $deposit->getCode();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_BOTTLES} = $deposit->getBottles();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_MATERIAL} = $deposit->getMaterial();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT_CASE} = $this->formatMoney($deposit->getDepositCase());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT_CASE_B2B} = $this->formatMoney($deposit->getDepositCaseB2b());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT_PER_BOTTLE} = $this->formatMoney($deposit->getDepositPerBottle());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_DEPOSIT_PER_BOTTLE_B2B} = $this->formatMoney($deposit->getDepositPerBottleB2b());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_VOLUME_PER_BOTTLE} = $this->formatLiter($deposit->getVolumePerBottle());
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_PRESENTATION_NAME} = $deposit->getPresentationName();
            $currentDeposit->{Response::KEY_DEPOSIT_ENTRIES_WEIGHT} = $this->formatWeight($deposit->getWeight());
            $currentDeposit->{Response::KEY_GTINS} = $deposit->getGtins();

            $responseDeposits[] = $currentDeposit;
        }

        return $responseDeposits;
    }

    /**
     * @param int $weight
     *
     * @return float
     */
    protected function formatWeight(int $weight = 0): float
    {
        return ($weight / 1000);
    }

    /**
     * @param int $volume
     *
     * @return float
     */
    protected function formatLiter(int $volume = 0): float
    {
        return ($volume / 1000);
    }

    /**
     * @param int $money
     *
     * @return float
     */
    protected function formatMoney(int $money): float
    {
        return $this->moneyPlugin->convertIntegerToDecimal($money);
    }
}
