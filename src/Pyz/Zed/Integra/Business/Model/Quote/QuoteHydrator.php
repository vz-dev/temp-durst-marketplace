<?php
/**
 * Durst - project - QuoteHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:52
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;

use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Integra\Business\Model\Quote\Deposit\DepositRepositoryInterface;
use Pyz\Zed\Integra\Business\Model\Quote\Product\ProductRepositoryInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;

class QuoteHydrator implements QuoteHydratorInterface
{
    protected const SINGLE_PRODUCT_ITEM_NAME_PREFIX = 'EINZELPRODUKT';
    protected const SUB_UNIT_PRODUCT_NAME_STR_FORMAT = '%s - %s - %s';

    /**
     * @var CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var AddressHydratorInterface
     */
    protected $addressHydrator;

    /**
     * @var PaymentHydratorInterface
     */
    protected $paymentHydrator;

    /**
     * @var TotalsHydratorInterface
     */
    protected $totalsHydrator;

    /**
     * @var CustomerHydratorInterface
     */
    protected $customerHydrator;

    /**
     * @var ItemsHydratorInterface
     */
    protected $itemsHydrator;

    /**
     * @var ExpensesHydratorInterface
     */
    protected $expensesHydrator;

    /**
     * @var DepositRepositoryInterface
     */
    protected $depositRepo;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepo;

    /**
     * QuoteHydrator constructor.
     *
     * @param CurrencyFacadeInterface $currencyFacade
     * @param AddressHydratorInterface $addressHydrator
     * @param PaymentHydratorInterface $paymentHydrator
     * @param TotalsHydratorInterface $totalsHydrator
     * @param CustomerHydratorInterface $customerHydrator
     * @param ItemsHydratorInterface $itemsHydrator
     * @param ExpensesHydratorInterface $expensesHydrator
     * @param DepositRepositoryInterface $depositRepo
     * @param ProductRepositoryInterface $productRepo
     */
    public function __construct(
        CurrencyFacadeInterface $currencyFacade,
        AddressHydratorInterface $addressHydrator,
        PaymentHydratorInterface $paymentHydrator,
        TotalsHydratorInterface $totalsHydrator,
        CustomerHydratorInterface $customerHydrator,
        ItemsHydratorInterface $itemsHydrator,
        ExpensesHydratorInterface $expensesHydrator,
        DepositRepositoryInterface $depositRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->currencyFacade = $currencyFacade;
        $this->addressHydrator = $addressHydrator;
        $this->paymentHydrator = $paymentHydrator;
        $this->totalsHydrator = $totalsHydrator;
        $this->customerHydrator = $customerHydrator;
        $this->itemsHydrator = $itemsHydrator;
        $this->expensesHydrator = $expensesHydrator;
        $this->depositRepo = $depositRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * @param int $idBranch
     * @param array $orderData
     *
     * @return QuoteTransfer
     */
    public function getQuote(int $idBranch, array $orderData): QuoteTransfer
    {
        $this->loadRepos($idBranch, $orderData);
        $quote = $this->createQuote($idBranch, $orderData);
        $quote->setCustomer($this->customerHydrator->createCustomerTransfer($orderData));

        foreach ($orderData['items'] as $positionDid => $item) {
            $this->setQuantityAndNameFromSubunitIfEmpty($item);
            $sku = $this->productRepo->getSkuForMerchantSku($item['merchant_sku'], $item['unit_type'], $idBranch);
            for ($i = 0; $i < $item['quantity']; $i++) {
                $quote->addItem($this->itemsHydrator->createItem($positionDid, $item, $sku));
                $quote->addExpense($this->expensesHydrator->createExpense($idBranch, $item, $sku, $i+1));
            }
        }

        $quote->setTotals($this->totalsHydrator->createTotalsTransfer($quote));

        return $quote;
    }

    /**
     * @param int $idBranch
     * @param array $orderData
     *
     * @return void
     */
    protected function loadRepos(
        int $idBranch,
        array $orderData
    ): void {
        $merchantSkus = [];
        foreach ($orderData['items'] as $positionDid => $item) {
            $merchantSkus[] = $item['merchant_sku'];
            $merchantSkus[] = sprintf('%s_%s', $item['merchant_sku'], $item['unit_type']);
        }

        $skus = $this->productRepo->loadSkus($idBranch, $merchantSkus);
        $this->depositRepo->loadDeposits($skus);
    }

    /**
     * @param int $idBranch
     * @param array $order
     *
     * @return QuoteTransfer
     */
    protected function createQuote(int $idBranch, array &$order): QuoteTransfer
    {
        $address = $this->addressHydrator->createAddressTransfer($order);
        $payment = $this->paymentHydrator->createPaymentTransfer();

        return (new QuoteTransfer())
            ->setFkBranch($idBranch)
            ->setIntegraReceiptDid($order['did'])
            ->setIntegraCustomerNo($order['customer_no'])
            ->setIntegraPaymentType($order['zahlArt'])
            ->setBillingAddress($address)
            ->setShippingAddress(clone $address)
            ->setCurrency($this->currencyFacade->getCurrent())
            ->addPayment($payment)
            ->setPriceMode(0)
            ->setPayment($payment)
            ->setOrderReference($order['reference'])
            ->setIntegraReceiptNo($order['receiptNo'])
            ->setIsExternal(true)
            ->setDeliveryOrder($order['nrTourFolge'])
            ->setFkConcreteTimeSlot($order['fk_concrete_time_slot']);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function setQuantityAndNameFromSubunitIfEmpty(array &$item) : array
    {
        if($item['quantity'] === 0){
            $item['quantity'] = $item['quantity_unit'];
            $item['name'] = sprintf(
                self::SUB_UNIT_PRODUCT_NAME_STR_FORMAT,
                self::SINGLE_PRODUCT_ITEM_NAME_PREFIX,
                $item['unit_type'],
                $item['name']
            );
        }

        return $item;
    }
}
