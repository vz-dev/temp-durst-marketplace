<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 15.01.19
 * Time: 11:13
 */

namespace Pyz\Zed\Oms\Business\Model\Durst;


use Generated\Shared\Transfer\DurstCompanyTransfer;
use Pyz\Zed\Oms\OmsConfig;

/**
 * Class DurstCompanyDetailsManager
 * @package Pyz\Zed\Oms\Business\Model\Durst
 */
class DurstCompanyDetailsManager implements DurstCompanyDetailsManagerInterface
{
    /**
     * @var OmsConfig
     */
    protected $config;

    public function __construct(
        OmsConfig $config
    )
    {
        $this->config = $config;
    }

    /**
     * @return DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer() : DurstCompanyTransfer
    {
        $durstCompanyTransfer = new DurstCompanyTransfer();

        $durstCompanyTransfer->setName($this->config->getDurstCompanyName());
        $durstCompanyTransfer->setStreet($this->config->getDurstCompanyStreet());
        $durstCompanyTransfer->setCity($this->config->getDurstCompanyCity());
        $durstCompanyTransfer->setWeb($this->config->getDurstCompanyWeb());
        $durstCompanyTransfer->setEmail($this->config->getDurstCompanyEmail());
        $durstCompanyTransfer->setVatId($this->config->getDurstCompanyVatId());
        $durstCompanyTransfer->setBio($this->config->getDurstCompanyBio());
        $durstCompanyTransfer->setJurisdiction($this->config->getDurstCompanyJurisdiction());
        $durstCompanyTransfer->setManagement($this->config->getDurstCompanyManagement());

        return $durstCompanyTransfer;
    }

}
