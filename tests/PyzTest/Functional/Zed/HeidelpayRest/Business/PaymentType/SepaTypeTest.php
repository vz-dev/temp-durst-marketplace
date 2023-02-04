<?php
namespace PyzTest\Functional\Zed\HeidelpayRest\Business\PaymentType;

use Codeception\Test\Unit;
use heidelpayPHP\Heidelpay;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\SepaType;

class SepaTypeTest extends Unit
{
    /**
     * @var \PyzTest\Functional\Zed\HeidelpayRest\HeidelpayRestBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\PaymentType\SepaTypeInterface
     */
    protected $sepaType;

    protected function _before()
    {
        $this->sepaType = new SepaType(
            $this->createHeidelpayClient()
        );
    }

    protected function _after()
    {
    }

    public function testGenerateSepaSandboxPaymentTypeIdGeneratesSandboxId()
    {
        $sepaTypeId = $this
            ->sepaType
            ->generateSepaSandboxPaymentTypeId();

        $this->assertStringStartsWith('s-sdd', $sepaTypeId);
    }

    /**
     * @return \heidelpayPHP\Heidelpay
     * @throws \RuntimeException
     */
    protected function createHeidelpayClient(): Heidelpay
    {
        return new Heidelpay(
            's-priv-2a10tPI14ymhn6vUNfuzFC0cTujELyYz',
            'de_DE'
        );
    }
}
