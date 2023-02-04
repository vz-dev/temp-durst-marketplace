<?php
namespace PyzTest\Functional\Zed\DepositMerchantConnector\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Merchant\Persistence\DstBranchToDeposit;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductLocalizedAttributes;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainer;
use Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranch;
use Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridge;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToDepositBridge;
use Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer\DepositMerchantConnectorToMerchantPriceBridge;
use Pyz\Zed\MerchantPrice\Persistence\MerchantPriceQueryContainer;
use Pyz\Zed\Tax\Business\TaxFacade;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacade;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group DepositMerchantConnector
 * @group Model
 * @group DepositBranchTest
 * Add your own group annotations below this line
 */
class DepositBranchTest extends Unit
{
    /**
     * @var \PyzTest\Functional\Zed\DepositMerchantConnector\DepositMerchantConnectorBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranchInterface
     */
    protected $depositBranchModel;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $localeTransfer;

    /**
     * @var \Pyz\Zed\DepositMerchantConnector\Dependency\Facade\DepositMerchantConnectorToLocaleBridgeInterface|MockObject
     */
    protected $localeFacade;

    /**
     * @var TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->localeTransfer = $this->createLocaleTransfer();
        $this->localeFacade = $this->createLocaleFacade();
        $this->taxFacade = $this->createTaxFacade();
        $this->depositBranchModel = $this->createDepositBranchModel();
    }

    /**
     * @return void
     */
    protected function _after()
    {
    }

    /**
     * @return void
     */
    public function testGetDepositsForBranchReturnsAllDepositsOfPassedBranch()
    {
        $this->addDepositForBranch();
        $this->addProductsWithAttributes();

        $this->localeFacade->expects($this->atLeastOnce())
            ->method('getCurrentLocale')
            ->will($this->returnValue($this->localeTransfer));

        $transfers = $this
            ->depositBranchModel
            ->getDepositsForBranch(
                (new BranchTransfer())
                ->setIdBranch(1)
            );

        $this->assertCount(2, $transfers);

        /** @var \Generated\Shared\Transfer\DepositTransfer $depositTransfer */
        $depositTransfer = $transfers[0];
        $this->assertSame('001', $depositTransfer->getCode());
        $this->assertSame('1 x 0.5L - Glas (pfandfrei)', $depositTransfer->getName());
        $this->assertSame(1, $depositTransfer->getIdDeposit());
        $this->assertSame(0, $depositTransfer->getDeposit());

        /** @var \Generated\Shared\Transfer\DepositTransfer $depositTransfer */
        $depositTransfer = $transfers[1];
        $this->assertSame('002', $depositTransfer->getCode());
        $this->assertSame('1 x 0.75L - Glas (pfandfrei)', $depositTransfer->getName());
        $this->assertSame(2, $depositTransfer->getIdDeposit());
        $this->assertSame(0, $depositTransfer->getDeposit());
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testGetDepositsForBranchReturnsOnlyGtinsForCurrentLocale()
    {
        $this->addDepositForBranch();
        $this->addProductsWithAttributes();

        $this->localeFacade->expects($this->atLeastOnce())
            ->method('getCurrentLocale')
            ->will($this->returnValue($this->localeTransfer));

        $transfers = $this
            ->depositBranchModel
            ->getDepositsForBranch(
                (new BranchTransfer())
                    ->setIdBranch(1)
            );

        $this->assertCount(2, $transfers);

        /** @var \Generated\Shared\Transfer\DepositTransfer $depositTransfer */
        $depositTransfer = $transfers[0];
        $this->assertSame('001', $depositTransfer->getCode());
        $this->assertSame('1 x 0.5L - Glas (pfandfrei)', $depositTransfer->getName());
        $this->assertSame(1, $depositTransfer->getIdDeposit());
        $this->assertSame(0, $depositTransfer->getDeposit());
        $gtins = $depositTransfer->getGtins();
        $this->assertCount(1, $gtins);
        $this->assertSame('11111111', $gtins[0]);

        /** @var \Generated\Shared\Transfer\DepositTransfer $depositTransfer */
        $depositTransfer = $transfers[1];
        $this->assertSame('002', $depositTransfer->getCode());
        $this->assertSame('1 x 0.75L - Glas (pfandfrei)', $depositTransfer->getName());
        $this->assertSame(2, $depositTransfer->getIdDeposit());
        $this->assertSame(0, $depositTransfer->getDeposit());
        $gtins = $depositTransfer->getGtins();
        $this->assertCount(0, $gtins);
    }

    /**
     * @return void
     */
    protected function addProductsWithAttributes(): void
    {
        $fkProductAbstract = $this->addProductAbstract();

        $product = new SpyProduct();
        $product->setFkDeposit(1);
        $product->setSku('AAAAAAAAAAAA');
        $product->setAttributes('{"name":"Reissdorf Kölsch 0,33l"}');
        $product->setFkProductAbstract($fkProductAbstract);
        $product->save();

        $price = new MerchantPrice();
        $price->setFkBranch(1);
        $price->setFkProduct($product->getIdProduct());
        $price->setIsActive(true);
        $price->setSku('activePriceFromCorrectBranch');
        $price->setMerchantSku('activePriceFromCorrectBranch');
        $price->save();

        $attributes = new SpyProductLocalizedAttributes();
        $attributes->setFkLocale($this->localeTransfer->getIdLocale());
        $attributes->setName('');
        $attributes->setAttributes('{"gtin":"11111111"}');
        $attributes->setIsComplete(true);
        $attributes->setFkProduct($product->getIdProduct());
        $attributes->setDescription('');
        $attributes->save();

        $attributes = new SpyProductLocalizedAttributes();
        $attributes->setFkLocale(12);
        $attributes->setName('');
        $attributes->setAttributes('{"gtin":"22222222222"}');
        $attributes->setIsComplete(true);
        $attributes->setFkProduct($product->getIdProduct());
        $attributes->setDescription('');
        $attributes->save();

        $product = new SpyProduct();
        $product->setFkDeposit(2);
        $product->setSku('BBBBBBBBBB');
        $product->setAttributes('{"name":"Reissdorf Kölsch 0,5l"}');
        $product->setFkProductAbstract($fkProductAbstract);
        $product->save();

        $price = new MerchantPrice();
        $price->setFkBranch(1);
        $price->setFkProduct($product->getIdProduct());
        $price->setIsActive(false);
        $price->setSku('inactivePriceFromCorrectBranch');
        $price->setMerchantSku('inactivePriceFromCorrectBranch');
        $price->save();

        $price = new MerchantPrice();
        $price->setFkBranch(2);
        $price->setFkProduct($product->getIdProduct());
        $price->setIsActive(true);
        $price->setSku('activePriceFromWrongBranch');
        $price->setMerchantSku('activePriceFromWrongBranch');
        $price->save();

        $attributes = new SpyProductLocalizedAttributes();
        $attributes->setFkLocale($this->localeTransfer->getIdLocale());
        $attributes->setName('');
        $attributes->setAttributes('{"gtin":"3333333"}');
        $attributes->setIsComplete(true);
        $attributes->setFkProduct($product->getIdProduct());
        $attributes->setDescription('');
        $attributes->save();
    }

    /**
     *
     * @return int
     */
    protected function addProductAbstract(): int
    {
        $entity = new SpyProductAbstract();
        $entity->setAttributes('{"name":"Reissdorf Kölsch"}');
        $entity->setSku('sdFSDFSDFSDFS');
        $entity->save();

        return $entity->getIdProductAbstract();
    }

    /**
     * @return void
     */
    protected function addDepositForBranch(): void
    {
        $entity = new DstBranchToDeposit();
        $entity->setFkBranch(1);
        $entity->setFkDeposit(1);
        $entity->setSku('full-unit-1');
        $entity->setSkuCase('case-1');
        $entity->setSkuBottle('bottle-1');
        $entity->save();

        $entity = new DstBranchToDeposit();
        $entity->setFkBranch(1);
        $entity->setFkDeposit(2);
        $entity->setSku('full-unit-2');
        $entity->setSkuCase('case-2');
        $entity->setSkuBottle('bottle-2');
        $entity->save();

        $entity = new DstBranchToDeposit();
        $entity->setFkBranch(2);
        $entity->setFkDeposit(1);
        $entity->setSku('full-unit-3');
        $entity->setSkuCase('case-3');
        $entity->setSkuBottle('bottle-3');
        $entity->save();
    }

    /**
     * @return MockObject
     */
    protected function createLocaleFacade()
    {
        return $this
            ->getMockBuilder(DepositMerchantConnectorToLocaleBridge::class)
            ->setConstructorArgs([new LocaleFacade()])
            ->setMethods(
                ['getCurrentLocale']
            )->getMock();
    }

    /**
     * @return TaxFacadeInterface|MockObject
     */
    protected function createTaxFacade(): TaxFacadeInterface
    {
        return $this
            ->getMockBuilder(TaxFacade::class)
            ->getMock();
    }

    /**
     * @return \Pyz\Zed\DepositMerchantConnector\Business\Model\DepositBranch
     */
    protected function createDepositBranchModel(): DepositBranch
    {
        return new DepositBranch(
            new DepositMerchantConnectorToDepositBridge(new DepositQueryContainer()),
            new DepositMerchantConnectorToMerchantPriceBridge(new MerchantPriceQueryContainer()),
            $this->localeFacade,
            $this->taxFacade
        );
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    protected function createLocaleTransfer(): LocaleTransfer
    {
        return (new LocaleTransfer())
            ->setIdLocale(46)
            ->setLocaleName('de_DE')
            ->setIsActive(true);
    }
}
