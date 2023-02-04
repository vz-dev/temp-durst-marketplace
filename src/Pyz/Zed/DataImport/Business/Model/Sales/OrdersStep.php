<?php
/**
 * Durst - project - OrdersStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-11
 * Time: 10:15
 */

namespace Pyz\Zed\DataImport\Business\Model\Sales;

use ArrayObject;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\DataImport\DataImportConfig;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException;
use Spryker\Zed\Cart\Business\CartFacadeInterface;
use Spryker\Zed\Checkout\Business\CheckoutFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class OrdersStep implements DataImportStepInterface
{
    protected const MAX_FAILS = 10;

    protected const COL_QUANTITY = 'quantity';
    protected const COL_ITEMS = 'items';
    protected const COL_BRANCH = 'branch';

    protected const COL_SHIPPING_SALUTATION = 'shipping_salutation';
    protected const COL_SHIPPING_EMAIL = 'shipping_email';
    protected const COL_SHIPPING_FIRST_NAME = 'shipping_first_name';
    protected const COL_SHIPPING_LAST_NAME = 'shipping_last_name';
    protected const COL_SHIPPING_ADDRESS_1 = 'shipping_address_1';
    protected const COL_SHIPPING_ADDRESS_2 = 'shipping_address_2';
    protected const COL_SHIPPING_ADDRESS_3 = 'shipping_address_3';
    protected const COL_SHIPPING_ZIP_CODE = 'shipping_zip_code';
    protected const COL_SHIPPING_CITY = 'shipping_city';
    protected const COL_SHIPPING_COMPANY = 'shipping_company';
    protected const COL_SHIPPING_PHONE = 'shipping_phone';

    protected const COL_BILLING_SALUTATION = 'billing_salutation';
    protected const COL_BILLING_EMAIL = 'billing_email';
    protected const COL_BILLING_FIRST_NAME = 'billing_first_name';
    protected const COL_BILLING_LAST_NAME = 'billing_last_name';
    protected const COL_BILLING_ADDRESS_1 = 'billing_address_1';
    protected const COL_BILLING_ADDRESS_2 = 'billing_address_2';
    protected const COL_BILLING_ADDRESS_3 = 'billing_address_3';
    protected const COL_BILLING_ZIP_CODE = 'billing_zip_code';
    protected const COL_BILLING_CITY = 'billing_city';
    protected const COL_BILLING_COMPANY = 'billing_company';
    protected const COL_BILLING_PHONE = 'billing_phone';

    protected const COL_COMMENT = 'comment';

    protected const ITEM_SKU = 'sku';
    protected const ITEM_QUANTITY = 'quantity';

    protected const COUNTRY_ISO2_CODE = 'DE';

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface
     */
    protected $heidelpayRestFacade;

    /**
     * @var \Spryker\Zed\Cart\Business\CartFacadeInterface
     */
    protected $cartFacade;

    /**
     * @var \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface
     */
    protected $checkoutFacade;

    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var \Pyz\Zed\DataImport\DataImportConfig
     */
    protected $config;

    /**
     * @var \Generated\Shared\Transfer\ConcreteTimeSlotTransfer[]
     */
    protected $concreteTimeSlot;

    /**
     * OrdersStep constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface $heidelpayRestFacade
     * @param \Spryker\Zed\Cart\Business\CartFacadeInterface $cartFacade
     * @param \Spryker\Zed\Checkout\Business\CheckoutFacadeInterface $checkoutFacade
     * @param \Spryker\Zed\Currency\Business\CurrencyFacadeInterface $currencyFacade
     * @param \Pyz\Zed\DataImport\DataImportConfig $config
     */
    public function __construct(
        HeidelpayRestFacadeInterface $heidelpayRestFacade,
        CartFacadeInterface $cartFacade,
        CheckoutFacadeInterface $checkoutFacade,
        CurrencyFacadeInterface $currencyFacade,
        DataImportConfig $config
    ) {
        $this->heidelpayRestFacade = $heidelpayRestFacade;
        $this->cartFacade = $cartFacade;
        $this->checkoutFacade = $checkoutFacade;
        $this->currencyFacade = $currencyFacade;
        $this->config = $config;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $data = $dataSet->getArrayCopy();

        if ($data[self::COL_QUANTITY] < 1) {
            return;
        }

        $i = 0;
        $fails = 0;
        $zipCode = $data[self::COL_SHIPPING_ZIP_CODE];
        $branch = $this->getBranchFromName($data[self::COL_BRANCH]);
        $this->setupConcreteTimeSlots($branch->getIdBranch(), $zipCode);

        $timeSlot = $this->getConcreteTimeSlot();
        while ($i < $data[self::COL_QUANTITY] && $fails < self::MAX_FAILS) {
            if ($timeSlot === null) {
                return;
            }

            $quoteTransfer = $this->calculate($data, $timeSlot, $branch);
            $response = $this->placeOrder($data, $quoteTransfer);

            //var_dump(print_r($response->getErrors(), true));
            if ($response->getIsSuccess() !== true) {
                $timeSlot = $this->getConcreteTimeSlot();
                $fails++;
                continue;
            }
            $i++;
        }
    }

    /**
     * @param array $data
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $timeSlot
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function placeOrder(
        array $data,
        QuoteTransfer $quoteTransfer
    ): CheckoutResponseTransfer {

        $grandTotal = $quoteTransfer
            ->getTotals()
            ->getGrandTotal();

        $payment = $this->getPayment($grandTotal);
        $quoteTransfer
            ->setFkBranch($this->getBranchFromName($data[self::COL_BRANCH])->getIdBranch())
            ->setBillingAddress($this->getBillingAddress($data))
            ->setShippingAddress($this->getShippingAddress($data))
            ->addPayment($payment)
            ->setPayment($payment)
            ->setCustomer($this->getCustomer($data));

        if (isset($data[self::COL_COMMENT]) === true) {
            $quoteTransfer->setComment($data[self::COL_COMMENT]);
        }

        return $this
            ->checkoutFacade
            ->placeOrder($quoteTransfer);
    }

    /**
     * @param array $data
     * @param \Generated\Shared\Transfer\ConcreteTimeSlotTransfer $timeSlot
     * @param \Generated\Shared\Transfer\BranchTransfer $branch
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function calculate(array $data, ConcreteTimeSlotTransfer $timeSlot, BranchTransfer $branch): QuoteTransfer
    {
        $quoteTransfer = (new QuoteTransfer())
            ->setItems(new ArrayObject())
            ->setCurrency(
                $this->currencyFacade->getCurrent()
            )
            ->setFkConcreteTimeSlot($timeSlot->getIdConcreteTimeSlot());

        $cartChangeTransfer = (new CartChangeTransfer())
            ->setBranch($branch)
            ->setItems(new ArrayObject($this->getItemsArrayFromString($data[self::COL_ITEMS])))
            ->setConcreteTimeSlot($timeSlot)
            ->setQuote($quoteTransfer);

        return $this
            ->cartFacade
            ->add($cartChangeTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer|null
     */
    protected function getConcreteTimeSlot(): ?ConcreteTimeSlotTransfer
    {
        return array_shift($this->concreteTimeSlot);
    }

    /**
     * @param int $idBranch
     * @param string $zipCode
     *
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException
     *
     * @return void
     */
    protected function setupConcreteTimeSlots(
        int $idBranch,
        string $zipCode
    ): void {
        $entities = SpyConcreteTimeSlotQuery::create()
            ->useSpyTimeSlotQuery()
                ->filterByFkBranch($idBranch)
                ->useSpyDeliveryAreaQuery()
                    ->filterByZipCode($zipCode)
                ->endUse()
            ->endUse()
            ->filterByFkConcreteTour(null, Criteria::ISNOTNULL)
            ->filterByStartTime(new DateTime('+1 day'), Criteria::GREATER_EQUAL)
            ->orderByStartTime(Criteria::ASC)
            ->find();

        if ($entities->count() === 0) {
            throw new ConcreteTimeSlotNotFoundException('No concrete time slot was found');
        }

        $this->concreteTimeSlot = [];
        foreach ($entities as $entity) {
            $this->concreteTimeSlot[] = $this->entityToTransfer($entity);
        }
    }

    /**
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    protected function getPayment(int $amount): PaymentTransfer
    {
        $heidelpayRestPayment = (new HeidelpayRestPaymentTransfer())
            ->setPaymentTypeId($this->heidelpayRestFacade->generateSepaSandboxPaymentTypeId());

        return (new PaymentTransfer())
            ->setHeidelpayRestPayment($heidelpayRestPayment)
            ->setPaymentSelection(HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT)
            ->setPaymentMethod(HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT)
            ->setPaymentProvider(HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_PROVIDER)
            ->setAmount($amount);
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function getCustomer(array $data): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setSalutation($data[self::COL_BILLING_SALUTATION])
            ->setFirstName($data[self::COL_BILLING_FIRST_NAME])
            ->setLastName($data[self::COL_BILLING_LAST_NAME])
            ->setEmail($data[self::COL_BILLING_EMAIL])
            ->setCompany($data[self::COL_BILLING_COMPANY])
            ->setPhone($data[self::COL_BILLING_PHONE])
            ->setIsGuest(true);
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getShippingAddress(array $data): AddressTransfer
    {
        return (new AddressTransfer())
            ->setSalutation($data[self::COL_SHIPPING_SALUTATION])
            ->setEmail($data[self::COL_SHIPPING_EMAIL])
            ->setFirstName($data[self::COL_SHIPPING_FIRST_NAME])
            ->setLastName($data[self::COL_SHIPPING_LAST_NAME])
            ->setAddress1($data[self::COL_SHIPPING_ADDRESS_1])
            ->setAddress2($data[self::COL_SHIPPING_ADDRESS_2])
            ->setAddress3($data[self::COL_SHIPPING_ADDRESS_3])
            ->setZipCode($data[self::COL_SHIPPING_ZIP_CODE])
            ->setCity($data[self::COL_SHIPPING_CITY])
            ->setCompany($data[self::COL_SHIPPING_COMPANY])
            ->setIso2Code(self::COUNTRY_ISO2_CODE)
            ->setPhone($data[self::COL_SHIPPING_PHONE]);
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getBillingAddress(array $data): AddressTransfer
    {
        return (new AddressTransfer())
            ->setSalutation($data[self::COL_BILLING_SALUTATION])
            ->setEmail($data[self::COL_BILLING_EMAIL])
            ->setFirstName($data[self::COL_BILLING_FIRST_NAME])
            ->setLastName($data[self::COL_BILLING_LAST_NAME])
            ->setAddress1($data[self::COL_BILLING_ADDRESS_1])
            ->setAddress2($data[self::COL_BILLING_ADDRESS_2])
            ->setAddress3($data[self::COL_BILLING_ADDRESS_3])
            ->setZipCode($data[self::COL_BILLING_ZIP_CODE])
            ->setCity($data[self::COL_BILLING_CITY])
            ->setCompany($data[self::COL_BILLING_COMPANY])
            ->setIso2Code(self::COUNTRY_ISO2_CODE)
            ->setPhone($data[self::COL_BILLING_PHONE]);
    }

    /**
     * @param string $name
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    protected function getBranchFromName(string $name): BranchTransfer
    {
        $entity = SpyBranchQuery::create()
            ->findOneByName($name);

        if(!$entity){
            throw new BranchNotFoundException();
        }

        return (new BranchTransfer())
            ->fromArray($entity->toArray(), true);
    }

    /**
     * @param string $itemsString
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getItemsArrayFromString(string $itemsString): array
    {
        $itemsArray = json_decode($itemsString, true);

        $itemsTransferArray = [];
        foreach ($itemsArray as $item) {
            $itemsTransferArray[] = (new ItemTransfer())
                ->setSku($item[self::ITEM_SKU])
                ->setQuantity($item[self::ITEM_QUANTITY]);
        }

        return $itemsTransferArray;
    }

    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot $entity
     *
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    protected function entityToTransfer(SpyConcreteTimeSlot $entity): ConcreteTimeSlotTransfer
    {
        $transfer = new ConcreteTimeSlotTransfer();
        $transfer->fromArray($entity->toArray(), true);
        $transfer->setTimeFormat($this->config->getDateTimeFormat());

        $timeSlot = $entity->getSpyTimeSlot();
        $transfer
            ->setMinUnits($timeSlot->getMinUnits())
            ->setMinValue($timeSlot->getMinValueFirst());

        if ($entity->getStartTime() !== null) {
            $startTime = $entity->getStartTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setStartTime($startTime->format($this->config->getDateTimeFormat()));
        }
        if ($entity->getEndTime() !== null) {
            $endTime = $entity->getEndTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setEndTime($endTime->format($this->config->getDateTimeFormat()));
        }

        return $transfer;
    }
}
