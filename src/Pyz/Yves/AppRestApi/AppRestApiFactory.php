<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 11:22
 */

namespace Pyz\Yves\AppRestApi;

use Exception;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Auth\AuthClientInterface;
use Pyz\Client\CancelOrder\CancelOrderClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Client\Deposit\DepositClientInterface;
use Pyz\Client\DepositMerchantConnector\DepositMerchantConnectorClientInterface;
use Pyz\Client\Discount\DiscountClientInterface;
use Pyz\Client\DriverApp\DriverAppClientInterface;
use Pyz\Client\HeidelpayRest\HeidelpayRestClientInterface;
use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Client\Oms\OmsClientInterface;
use Pyz\Client\ProductGtin\ProductGtinClientInterface;
use Pyz\Client\TermsOfService\TermsOfServiceClientInterface;
use Pyz\Client\Tour\TourClientInterface;
use Pyz\Yves\AppRestApi\Handler\BranchRequestHandler;
use Pyz\Yves\AppRestApi\Handler\CategoryRequestHandler;
use Pyz\Yves\AppRestApi\Handler\CityMerchantRequestHandler;
use Pyz\Yves\AppRestApi\Handler\CityRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DeliveryAreaRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DepositPickupCreateInquiryRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DiscountRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverBranchRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverCancelOrderRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverCloseOrderRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverDepositRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverDownloadLatestReleaseHandler;
use Pyz\Yves\AppRestApi\Handler\DriverGtinRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverLatestReleaseRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverLoginRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverLogoutRequestHandler;
use Pyz\Yves\AppRestApi\Handler\DriverTourRequestHandler;
use Pyz\Yves\AppRestApi\Handler\GraphmastersRequestHandler;
use Pyz\Yves\AppRestApi\Handler\GraphmastersSettingsRequestHandler;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Branch\CategoriesHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Branch\DiscountHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Branch\MerchantsHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Branch\PaymentProviderHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Category\CategoryHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\City\CitynameHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\City\MerchantsHydrator as CityMerchantsHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\City\PaymentProviderHydrator as CityPaymentProviderHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DeliveryArea\BranchDeliversHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DeliveryArea\CityHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DepositPickup\DepositPickupCreateInquiryHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Discount\DiscountVoucherHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\CancelOrderHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\CloseOrderHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\DepositHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\LoginHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\LogoutHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\ProductGtinHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\DriverApp\TourHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Graphmasters\GMSettingsHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Graphmasters\TimeSlotHydrator as GraphmastersTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Product\DiscountHydrator as MerchantProductDiscountHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Product\ProductHydrator as MerchantProductHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Products\CategoriesHydrator as MerchantProductsCategoriesHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Products\DiscountHydrator as MerchantProductsDiscountHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\TimeSlotHydrator as MerchantTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\AddressHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\BranchHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\ClientHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\CommentHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\ConcreteTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\CustomerHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\GMTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator\PaymentHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Overview\AddressHydrator as OverviewAddressHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Overview\ExpenseHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Overview\TimeSlotHydrator as OverviewTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot\CheapestTimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot\TermsHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\TimeSlot\TimeSlotHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\VersionedHydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Voucher\MerchantHydrator;
use Pyz\Yves\AppRestApi\Handler\MerchantProductRequestHandler;
use Pyz\Yves\AppRestApi\Handler\MerchantProductsRequestHandler;
use Pyz\Yves\AppRestApi\Handler\MerchantTimeSlotRequestHandler;
use Pyz\Yves\AppRestApi\Handler\OrderRequestHandler;
use Pyz\Yves\AppRestApi\Handler\OverviewRequestHandler;
use Pyz\Yves\AppRestApi\Handler\PaymentStatusByOrderRefRequestHandler;
use Pyz\Yves\AppRestApi\Handler\RequestHandlerInterface;
use Pyz\Yves\AppRestApi\Handler\TimeSlotRequestHandler;
use Pyz\Yves\AppRestApi\Handler\VoucherRequestHandler;
use Pyz\Yves\AppRestApi\Log\AnalyticsBranchFormatter;
use Pyz\Yves\AppRestApi\Log\AnalyticsBranchLogConfig;
use Pyz\Yves\AppRestApi\Log\AnalyticsMerchantTimeSlotFormatter;
use Pyz\Yves\AppRestApi\Log\AnalyticsMerchantTimeSlotLogConfig;
use Pyz\Yves\AppRestApi\Log\AnalyticsOverviewFormatter;
use Pyz\Yves\AppRestApi\Log\AnalyticsOverviewLogConfig;
use Pyz\Yves\AppRestApi\Log\AnalyticsTimeSlotFormatter;
use Pyz\Yves\AppRestApi\Log\AnalyticsTimeSlotLogConfig;
use Spryker\Client\Checkout\CheckoutClientInterface;
use Spryker\Client\Price\PriceClientInterface;
use Spryker\Client\Shipment\ShipmentClientInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Yves\Money\Plugin\MoneyPlugin;

/**
 * Class AppRestApiFactory
 * @package Pyz\Yves\AppRestApi
 * @method AppRestApiConfig getConfig()
 */
class AppRestApiFactory extends AbstractFactory
{
    /**
     * @return BranchRequestHandler
     */
    public function createBranchRequestHandler(): BranchRequestHandler
    {
        return new BranchRequestHandler(
            $this->getConfig(),
            [
                $this->createMerchantsHydrator(),
                $this->createPaymentProviderHydrator(),
                $this->createCategoriesHydrator(),
                $this->createDiscountHydrator(),
            ],
            $this->createAnalyticsBranchLogConfig()
        );
    }

    /**
     * @return CityRequestHandler
     */
    public function createCityRequestHandler(): CityRequestHandler
    {
        return new CityRequestHandler(
            $this->getConfig(),
            [
                $this->createCityHydrator(),
            ]
        );
    }

    /**
     * @return OrderRequestHandler
     */
    public function createOrderRequestHandler(): OrderRequestHandler
    {
        return new OrderRequestHandler(
            $this->getConfig(),
            [
                $this->createQuoteHydrator(),
            ]
        );
    }

    /**
     * @return TimeSlotRequestHandler
     */
    public function createTimeSlotRequestHandler(): TimeSlotRequestHandler
    {
        return new TimeSlotRequestHandler(
            $this->getConfig(),
            $this->createAnalyticsTimeSlotLogConfig(),
            $this->createTimeSlotHydratorStack()
        );
    }

    /**
     * @return VoucherRequestHandler
     */
    public function createVoucherRequestHandler(): VoucherRequestHandler
    {
        return new VoucherRequestHandler(
            $this->getConfig(),
            [
                $this->createMerchantHydrator(),
            ]
        );
    }

    /**
     * @return CategoryRequestHandler
     */
    public function createCategoryRequestHandler(): CategoryRequestHandler
    {
        return new CategoryRequestHandler(
            $this->getConfig(),
            [
                $this->createCategoryHydrator(),
            ]
        );
    }

    /**
     * @return HydratorInterface|DiscountRequestHandler
     */
    public function createDiscountRequestHandler(): HydratorInterface
    {
        return new DiscountRequestHandler(
            $this->getConfig(),
            [
                $this->createDiscountVoucherHydrator()
            ]
        );
    }

    /**
     * @return DeliveryAreaRequestHandler
     */
    public function createDeliveryAreaRequestHandler(): DeliveryAreaRequestHandler
    {
        return new DeliveryAreaRequestHandler(
            $this->getConfig(),
            [
                $this->createBranchDeliversHydrator()
            ]
        );
    }

    /**
     * @return PaymentStatusByOrderRefRequestHandler
     */
    public function createPaymentStatusByOrderRefRequestHandler(): PaymentStatusByOrderRefRequestHandler
    {
        return new PaymentStatusByOrderRefRequestHandler(
            $this->getHeidelpayRestClient(),
            $this->getConfig()
        );
    }

    /**
     * @return DepositPickupCreateInquiryRequestHandler
     */
    public function createDepositPickupCreateInquiryRequestHandler(): DepositPickupCreateInquiryRequestHandler
    {
        return new DepositPickupCreateInquiryRequestHandler(
            $this->getConfig(),
            [$this->createDepositPickupCreateInquiryHydrator()]
        );
    }

    /**
     * @return DriverCancelOrderRequestHandler
     */
    public function createDriverCancelOrderRequestHandler(): DriverCancelOrderRequestHandler
    {
        return new DriverCancelOrderRequestHandler(
            $this
                ->getConfig(),
            $this
                ->createDriverCancelOrderStack()
        );
    }

    /**
     * @return LoggerConfigInterface
     */
    protected function createAnalyticsBranchLogConfig(): LoggerConfigInterface
    {
        return new AnalyticsBranchLogConfig(
            [
                $this->createBranchLogStreamHandler(),
            ]
        );
    }

    /**
     * @return LoggerConfigInterface
     */
    protected function createAnalyticsTimeSlotLogConfig(): LoggerConfigInterface
    {
        return new AnalyticsTimeSlotLogConfig(
            [
                $this->createTimeSlotLogStreamHandler(),
            ]
        );
    }

    /**
     * @return MerchantsHydrator
     */
    protected function createMerchantsHydrator(): MerchantsHydrator
    {
        return new MerchantsHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return CityHydrator
     */
    protected function createCityHydrator(): CityHydrator
    {
        return new CityHydrator(
            $this->getDeliveryAreaClient()
        );
    }

    /**
     * @return PaymentProviderHydrator
     */
    protected function createPaymentProviderHydrator(): PaymentProviderHydrator
    {
        return new PaymentProviderHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return CategoriesHydrator
     */
    protected function createCategoriesHydrator(): CategoriesHydrator
    {
        return new CategoriesHydrator(
            $this->getAppRestApiClient(),
            $this->getMoneyPlugin(),
            $this->getConfig()
        );
    }

    /**
     * @return MerchantHydrator
     */
    protected function createMerchantHydrator(): MerchantHydrator
    {
        return new MerchantHydrator(
            $this->getAppRestApiClient()
        );
    }

    /**
     * @return CategoryHydrator
     */
    protected function createCategoryHydrator(): CategoryHydrator
    {
        return new CategoryHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return BranchDeliversHydrator
     */
    protected function createBranchDeliversHydrator(): BranchDeliversHydrator
    {
        return new BranchDeliversHydrator(
            $this->getDeliveryAreaClient()
        );
    }

    /**
     * @return QuoteHydrator
     */
    protected function createQuoteHydrator(): QuoteHydrator
    {
        return new QuoteHydrator(
            $this->getQuoteHydratorStack(),
            $this->getAppRestApiClient(),
            $this->getDeliveryAreaClient(),
            $this->getCartClient(),
            $this->getCheckoutClient(),
            $this->getSalesClient()
        );
    }

    /**
     * @return QuoteHydrator\QuoteHydratorInterface[]
     */
    protected function getQuoteHydratorStack(): array
    {
        return [
            $this->createBranchHydrator(),
            $this->createAddressHydrator(),
            $this->createPaymentHydrator(),
            $this->createCustomerHydrator(),
            $this->createConcreteTimeSlotHydrator(),
            $this->createCommentHydrator(),
            $this->createClientHydrator(),
            $this->createGMTimeSlotHydrator()
        ];
    }

    /**
     * @return QuoteHydrator\AddressHydrator
     */
    protected function createAddressHydrator(): AddressHydrator
    {
        return new AddressHydrator();
    }

    /**
     * @return QuoteHydrator\BranchHydrator
     */
    protected function createBranchHydrator(): BranchHydrator
    {
        return new BranchHydrator();
    }

    /**
     * @return QuoteHydrator\ConcreteTimeSlotHydrator
     */
    protected function createConcreteTimeSlotHydrator(): ConcreteTimeSlotHydrator
    {
        return new ConcreteTimeSlotHydrator();
    }

    /**
     * @return QuoteHydrator\CustomerHydrator
     */
    protected function createCustomerHydrator(): CustomerHydrator
    {
        return new CustomerHydrator();
    }

    /**
     * @return QuoteHydrator\PaymentHydrator
     */
    protected function createPaymentHydrator(): PaymentHydrator
    {
        return new PaymentHydrator();
    }

    /**
     * @return QuoteHydrator\ClientHydrator
     */
    protected function createClientHydrator(): ClientHydrator
    {
        return new ClientHydrator();
    }

    /**
     * @return GMTimeSlotHydrator
     */
    protected function createGMTimeSlotHydrator(): GMTimeSlotHydrator
    {
        return new GMTimeSlotHydrator();
    }

    /**
     * @return AppRestApiClientInterface
     */
    protected function getAppRestApiClient(): AppRestApiClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_APP_REST_API);
    }

    /**
     * @return CartClientInterface
     */
    protected function getCartClient(): CartClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_CART);
    }

    /**
     * @return CheckoutClientInterface
     */
    protected function getCheckoutClient(): CheckoutClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_CHECKOUT);
    }

    /**
     * @return ShipmentClientInterface
     */
    protected function getShipmentClient(): ShipmentClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_SHIPMENT);
    }

    /**
     * @return PriceClientInterface
     */
    protected function getPriceClient(): PriceClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_PRICE);
    }

    /**
     * @return DeliveryAreaClientInterface
     */
    protected function getDeliveryAreaClient(): DeliveryAreaClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_DELIVERY_AREA);
    }

    /**
     * @return TermsOfServiceClientInterface
     */
    protected function getTermsOfServiceClient(): TermsOfServiceClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_TERMS_OF_SERVICE);
    }

    /**
     * @return MerchantClientInterface
     */
    protected function getMerchantClient(): MerchantClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_MERCHANT);
    }

    /**
     * @return MoneyPlugin
     */
    protected function getMoneyPlugin(): MoneyPlugin
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::PLUGIN_MONEY);
    }

    /**
     * @return HandlerInterface
     */
    protected function createBranchLogStreamHandler(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this->getConfig()->getAnalyticsBranchLogFilePath(),
            Logger::INFO
        );

        $handler->setFormatter($this->createAnalyticsBranchFormatter());

        return $handler;
    }

    /**
     * @return AnalyticsBranchFormatter
     */
    protected function createAnalyticsBranchFormatter(): AnalyticsBranchFormatter
    {
        return new AnalyticsBranchFormatter();
    }

    /**
     * @return AnalyticsTimeSlotFormatter
     */
    protected function createAnalyticsTimeSlotFormatter(): AnalyticsTimeSlotFormatter
    {
        return new AnalyticsTimeSlotFormatter();
    }

    /**
     * @return HandlerInterface
     */
    protected function createTimeSlotLogStreamHandler(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this->getConfig()->getAnalyticsTimeSlotLogFilePath(),
            Logger::INFO
        );

        $handler->setFormatter($this->createAnalyticsTimeSlotFormatter());

        return $handler;
    }

    /**
     * @return mixed
     */
    protected function getSalesClient()
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_SALES);
    }

    /**
     * @return CancelOrderClientInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getCancelOrderClient(): CancelOrderClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_CANCEL_ORDER
            );
    }

    /**
     * @return CommentHydrator
     */
    protected function createCommentHydrator(): CommentHydrator
    {
        return new CommentHydrator();
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createTimeSlotHydratorStack(): array
    {
        return [
            $this->createTimeSlotHydrator(),
            $this->createCheapestTimeSlotHydrator(),
            $this->createTermsHydrator(),
        ];
    }

    /**
     * @return HydratorInterface
     */
    protected function createTimeSlotHydrator(): HydratorInterface
    {
        return new TimeSlotHydrator(
            $this->getConfig(),
            $this->getAppRestApiClient(),
            $this->getCartClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return HydratorInterface
     */
    protected function createCheapestTimeSlotHydrator(): HydratorInterface
    {
        return new CheapestTimeSlotHydrator();
    }

    /**
     * @return HydratorInterface
     */
    protected function createTermsHydrator(): HydratorInterface
    {
        return new TermsHydrator(
            $this->getTermsOfServiceClient()
        );
    }

    /**
     * @return HeidelpayRestClientInterface
     */
    public function getHeidelpayRestClient(): HeidelpayRestClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_HEIDELPAY_REST);
    }

    /**
     * @return DiscountHydrator
     */
    protected function createDiscountHydrator(): DiscountHydrator
    {
        return new DiscountHydrator(
            $this->getAppRestApiClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return DepositClientInterface
     */
    protected function getDepositClient(): DepositClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_DEPOSIT);
    }

    /**
     * @return TourClientInterface
     */
    protected function getTourClient(): TourClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_TOUR);
    }

    /**
     * @return ProductGtinClientInterface
     */
    protected function getProductGtinClient(): ProductGtinClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_GTIN);
    }

    /**
     * @return AuthClientInterface
     */
    protected function getAuthClient(): AuthClientInterface
    {
        return $this
            ->getProvidedDependency(AppRestApiDependencyProvider::CLIENT_AUTH);
    }

    /**
     * @return DriverLoginRequestHandler
     */
    public function createDriverLoginRequestHandler(): DriverLoginRequestHandler
    {
        return new DriverLoginRequestHandler(
            $this->getConfig(),
            $this->createDriverAppLoginHydratorStack()
        );
    }

    /**
     * @return DriverLogoutRequestHandler
     */
    public function createDriverLogoutRequestHandler(): DriverLogoutRequestHandler
    {
        return new DriverLogoutRequestHandler(
            $this->getConfig(),
            $this->createDriverAppLogoutHydratorStack()
        );
    }

    /**
     * @return DriverCloseOrderRequestHandler
     */
    public function createDriverCloseOrderRequestHandler(): DriverCloseOrderRequestHandler
    {
        return new DriverCloseOrderRequestHandler(
            $this->getConfig(),
            $this->createDriverAppCloseOrderHydratorStack()
        );
    }

    /**
     * @return DriverDepositRequestHandler
     */
    public function createDriverDepositRequestHandler(): DriverDepositRequestHandler
    {
        return new DriverDepositRequestHandler(
            $this->getConfig(),
            $this->createDriverAppDepositHydratorStack()
        );
    }

    /**
     * @return DriverGtinRequestHandler
     */
    public function createDriverGtinRequestHandler(): DriverGtinRequestHandler
    {
        return new DriverGtinRequestHandler(
            $this->getConfig(),
            $this->createDriverAppGtinHydratorStack()
        );
    }

    /**
     * @return DriverTourRequestHandler
     */
    public function createDriverTourRequestHandler(): DriverTourRequestHandler
    {
        return new DriverTourRequestHandler(
            $this->getConfig(),
            $this->createDriverAppTourHydratorStack()
        );
    }

    /**
     * @return RequestHandlerInterface
     */
    public function createDriverBranchRequestHandler(): RequestHandlerInterface
    {
        return new DriverBranchRequestHandler(
            $this->getConfig(),
            $this->getMerchantClient()
        );
    }

    /**
     * @return RequestHandlerInterface
     */
    public function createDriverLatestReleaseRequestHandler(): RequestHandlerInterface
    {
        return new DriverLatestReleaseRequestHandler(
            $this->getConfig(),
            $this->getDriverAppClient(),
            $this->getAuthClient()
        );
    }

    /**
     * @return DriverDownloadLatestReleaseHandler
     */
    public function createDriverDownloadLatestReleaseRequestHandler(): DriverDownloadLatestReleaseHandler
    {
        return new DriverDownloadLatestReleaseHandler(
            $this->getConfig(),
            $this->getDriverAppClient(),
            $this->getAuthClient()
        );
    }

    /**
     * @return OmsClientInterface
     */
    protected function getOmsClient(): OmsClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_OMS
            );
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppDepositHydratorStack(): array
    {
        return [
            $this->createDriverAppDepositHydrator(),
        ];
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppTourHydratorStack(): array
    {
        return [
            $this->createDriverAppTourHydrator(),
        ];
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppGtinHydratorStack(): array
    {
        return [
            $this->createDriverAppGtinHydrator(),
        ];
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppLoginHydratorStack(): array
    {
        return [
            $this->createDriverAppLoginHydrator(),
        ];
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppLogoutHydratorStack(): array
    {
        return [
            $this->createDriverAppLogoutHydrator(),
        ];
    }

    /**
     * @return HydratorInterface[]
     */
    protected function createDriverAppCloseOrderHydratorStack(): array
    {
        return [
            $this->createDriverAppCloseOrderHydrator(),
        ];
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppDepositHydrator(): HydratorInterface
    {
        return new DepositHydrator(
            $this->getDepositMerchantConnectorClient(),
            $this->getMoneyPlugin(),
            $this->getMerchantClient(),
            $this->getAuthClient(),
            $this->getDriverAppClient()
        );
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppGtinHydrator(): HydratorInterface
    {
        return new ProductGtinHydrator(
            $this->getProductGtinClient(),
            $this->getAuthClient()
        );
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppTourHydrator(): HydratorInterface
    {
        return new TourHydrator(
            $this->getTourClient(),
            $this->getAuthClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppLoginHydrator(): HydratorInterface
    {
        return new LoginHydrator(
            $this->getAuthClient()
        );
    }

    /**
     * @return DepositMerchantConnectorClientInterface
     */
    protected function getDepositMerchantConnectorClient(): DepositMerchantConnectorClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_DEPOSIT_MERCHANT_CONNECTOR
            );
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppLogoutHydrator(): HydratorInterface
    {
        return new LogoutHydrator(
            $this->getAuthClient()
        );
    }

    /**
     * @return HydratorInterface
     */
    protected function createDriverAppCloseOrderHydrator(): HydratorInterface
    {
        return new CloseOrderHydrator(
            $this->getSalesClient(),
            $this->getMerchantClient(),
            $this->getOmsClient(),
            $this->getAuthClient(),
            $this->getConfig()
        );
    }

    /**
     * @return DriverAppClientInterface
     */
    protected function getDriverAppClient(): DriverAppClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_DRIVER_APP
            );
    }

    /**
     * @return HydratorInterface|CityMerchantRequestHandler
     */
    public function createCityMerchantRequestHydrator(): HydratorInterface
    {
        return new CityMerchantRequestHandler(
            $this->getConfig(),
            [
                $this
                    ->createCitynameHydrator(),
                $this
                    ->createCityMerchantsHydrator(),
                $this
                    ->createCityPaymentProviderHydrator(),
            ]
        );
    }

    /**
     * @return HydratorInterface|CitynameHydrator
     */
    protected function createCitynameHydrator(): HydratorInterface
    {
        return new CitynameHydrator(
            $this->getDeliveryAreaClient()
        );
    }

    /**
     * @return HydratorInterface|CityMerchantsHydrator
     */
    protected function createCityMerchantsHydrator(): HydratorInterface
    {
        return new CityMerchantsHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return HydratorInterface|CityPaymentProviderHydrator
     */
    protected function createCityPaymentProviderHydrator(): HydratorInterface
    {
        return new CityPaymentProviderHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @param string $version
     *
     * @return VersionedHydratorInterface|MerchantProductsRequestHandler
     *
     * @throws Exception
     */
    public function createMerchantProductsRequestHydrator(string $version): VersionedHydratorInterface
    {
        return new MerchantProductsRequestHandler(
            $this->getConfig(),
            [
                $this->createMerchantProductsCategoriesHydrator(),
                $this->createMerchantProductsDiscountHydrator(),
            ]
        );
    }

    /**
     * @return VersionedHydratorInterface|MerchantProductsCategoriesHydrator
     */
    protected function createMerchantProductsCategoriesHydrator(): VersionedHydratorInterface
    {
        return new MerchantProductsCategoriesHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return VersionedHydratorInterface|MerchantProductsDiscountHydrator
     */
    protected function createMerchantProductsDiscountHydrator(): VersionedHydratorInterface
    {
        return new MerchantProductsDiscountHydrator(
            $this->getAppRestApiClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return HydratorInterface|MerchantProductRequestHandler
     *
     * @throws Exception
     */
    public function createMerchantProductRequestHandler(): HydratorInterface
    {
        return new MerchantProductRequestHandler(
            $this->getConfig(),
            [
                $this->createMerchantProductHydrator(),
                $this->createMerchantProductDiscountHydrator(),
            ]
        );
    }

    /**
     * @return HydratorInterface|MerchantProductHydrator
     */
    protected function createMerchantProductHydrator(): HydratorInterface
    {
        return new MerchantProductHydrator(
            $this->getAppRestApiClient(),
            $this->getConfig()
        );
    }

    /**
     * @return HydratorInterface|MerchantProductDiscountHydrator
     */
    protected function createMerchantProductDiscountHydrator(): HydratorInterface
    {
        return new MerchantProductDiscountHydrator(
            $this->getAppRestApiClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return RequestHandlerInterface|MerchantTimeSlotRequestHandler
     */
    public function createMerchantTimeSlotRequestHandler(): RequestHandlerInterface
    {
        return new MerchantTimeSlotRequestHandler(
            $this->getConfig(),
            $this->createAnalyticsMerchantTimeSlotLogConfig(),
            [
                $this->createMerchantTimeSlotTimeSlotHydrator(),
            ]
        );
    }

    /**
     * @return LoggerConfigInterface
     */
    protected function createAnalyticsMerchantTimeSlotLogConfig(): LoggerConfigInterface
    {
        return new AnalyticsMerchantTimeSlotLogConfig(
            [
                $this->createMerchantTimeSlotLogStreamHandler(),
            ]
        );
    }

    /**
     * @return HandlerInterface
     */
    protected function createMerchantTimeSlotLogStreamHandler(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this
                ->getConfig()
                ->getAnalyticsMerchantTimeSlotLogFilePath(),
            Logger::INFO
        );

        $handler->setFormatter($this->createAnalyticsMerchantTimeSlotFormatter());

        return $handler;
    }

    /**
     * @return AnalyticsMerchantTimeSlotFormatter
     */
    protected function createAnalyticsMerchantTimeSlotFormatter(): AnalyticsMerchantTimeSlotFormatter
    {
        return new AnalyticsMerchantTimeSlotFormatter();
    }

    /**
     * @return HydratorInterface|MerchantTimeSlotHydrator
     */
    protected function createMerchantTimeSlotTimeSlotHydrator(): HydratorInterface
    {
        return new MerchantTimeSlotHydrator(
            $this->getConfig(),
            $this->getAppRestApiClient(),
            $this->getCartClient()
        );
    }

    /**
     * @return RequestHandlerInterface|OverviewRequestHandler
     */
    public function createOverviewRequestHandler(): RequestHandlerInterface
    {
        return new OverviewRequestHandler(
            $this->getConfig(),
            [
                $this->createOverviewAddressHydrator(),
                $this->createOverViewTimeSlotHydrator(),
                $this->createOverviewExpenseHydrator(),
            ]
        );
    }

    /**
     * @return LoggerConfigInterface
     */
    protected function createAnalyticsOverviewLogConfig(): LoggerConfigInterface
    {
        return new AnalyticsOverviewLogConfig(
            [
                $this->createOverviewLogStreamHandler(),
            ]
        );
    }

    /**
     * @return HandlerInterface
     */
    protected function createOverviewLogStreamHandler(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this
                ->getConfig()
                ->getAnalyticsOverviewLogFilePath(),
            Logger::INFO
        );

        $handler->setFormatter($this->createAnalyticsOverviewFormatter());

        return $handler;
    }

    /**
     * @return AnalyticsOverviewFormatter
     */
    protected function createAnalyticsOverviewFormatter(): AnalyticsOverviewFormatter
    {
        return new AnalyticsOverviewFormatter();
    }

    /**
     * @return HydratorInterface|OverviewTimeSlotHydrator
     */
    protected function createOverViewTimeSlotHydrator(): HydratorInterface
    {
        return new OverviewTimeSlotHydrator(
            $this->getAppRestApiClient()
        );
    }

    /**
     * @return HydratorInterface|ExpenseHydrator
     */
    protected function createOverviewExpenseHydrator(): HydratorInterface
    {
        return new ExpenseHydrator(
            $this->getConfig(),
            $this->getAppRestApiClient(),
            $this->getCartClient(),
            $this->getMoneyPlugin()
        );
    }

    /**
     * @return HydratorInterface|OverviewAddressHydrator
     */
    protected function createOverviewAddressHydrator(): HydratorInterface
    {
        return new OverviewAddressHydrator();
    }

    /**
     * @return HydratorInterface|DiscountVoucherHydrator
     * @throws ContainerKeyNotFoundException
     */
    protected function createDiscountVoucherHydrator(): HydratorInterface
    {
        return new DiscountVoucherHydrator(
            $this->getDiscountClient(),
            $this->getCartClient(),
            $this->getAppRestApiClient(),
            $this->getDeliveryAreaClient()
        );
    }

    /**
     * @return DiscountClientInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDiscountClient(): DiscountClientInterface
    {
        return $this
            ->getProvidedDependency(
                AppRestApiDependencyProvider::CLIENT_DISCOUNT
            );
    }

    /**
     * @return HydratorInterface|DepositPickupCreateInquiryHydrator
     */
    protected function createDepositPickupCreateInquiryHydrator(): HydratorInterface
    {
        return new DepositPickupCreateInquiryHydrator($this->getAppRestApiClient());
    }

    /**
     * @return array|HydratorInterface[]
     */
    protected function createDriverCancelOrderStack(): array
    {
        return [
            $this
                ->createDriverCancelOrderHydrator()
        ];
    }

    /**
     * @return HydratorInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function createDriverCancelOrderHydrator(): HydratorInterface
    {
        return new CancelOrderHydrator(
            $this
                ->getSalesClient(),
            $this
                ->getAuthClient(),
            $this
                ->getCancelOrderClient(),
            $this
                ->getConfig()
        );
    }

    /**
     * @return RequestHandlerInterface|GraphmastersRequestHandler
     */
    public function createGraphmastersRequestHandler(): RequestHandlerInterface
    {
        return new GraphmastersRequestHandler(
            $this->getConfig(),
            [
                $this->createGraphmastersTimeSlotHydrator(),
            ]
        );
    }

    /**
     * @return HydratorInterface|GraphmastersTimeSlotHydrator
     */
    protected function createGraphmastersTimeSlotHydrator(): HydratorInterface
    {
        return new GraphmastersTimeSlotHydrator(
            $this->getConfig(),
            $this->getAppRestApiClient(),
            $this->getCartClient()
        );
    }

    /**
     * @return RequestHandlerInterface
     */
    public function createGraphmastersSettingsRequestHandler(): RequestHandlerInterface
    {
        return new GraphmastersSettingsRequestHandler(
            $this->getConfig(),
            [
                $this->createGraphmastersSettingsHydrator(),
            ]
        );
    }

    /**
     * @return HydratorInterface|GraphmastersTimeSlotHydrator
     */
    protected function createGraphmastersSettingsHydrator(): HydratorInterface
    {
        return new GMSettingsHydrator(
            $this->getConfig(),
            $this->getAppRestApiClient()
        );
    }
}
