<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:57
 */

namespace Pyz\Zed\TermsOfService\Persistence;


use Orm\Zed\TermsOfService\Persistence\SpyMerchantToTermsOfServiceQuery;
use Orm\Zed\TermsOfService\Persistence\SpyTermsOfServiceQuery;

interface TermsOfServiceQueryContainerInterface
{
    /**
     * @return SpyTermsOfServiceQuery
     */
    public function queryTermsOfService();

    /**
     * @return SpyMerchantToTermsOfServiceQuery
     */
    public function queryMerchantToTermsOfService();

    /**
     * @param $acceptedTermsOfService
     * @param string $customerTermsName
     * @return SpyTermsOfServiceQuery
     */
    public function queryUnacceptedTermsOfService($acceptedTermsOfService, string $customerTermsName) : SpyTermsOfServiceQuery;

    /**
     * @param int $idMerchant
     * @return SpyTermsOfServiceQuery
     */
    public function queryAcceptedTermsOfServiceByIdMerchant(int $idMerchant) : SpyTermsOfServiceQuery;

    /**
     * @return SpyTermsOfServiceQuery
     */
    public function queryActiveTermsOfService() : SpyTermsOfServiceQuery;

    /**
     * @param int $timestamp
     * @param string $customerTermsName
     * @return SpyTermsOfServiceQuery
     */
    public function queryActiveCustomerTermsByTimestamp(int $timestamp, string $customerTermsName) : SpyTermsOfServiceQuery;
}
