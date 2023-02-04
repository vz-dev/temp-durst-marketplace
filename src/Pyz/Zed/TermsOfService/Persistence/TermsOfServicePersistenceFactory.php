<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:42
 */

namespace Pyz\Zed\TermsOfService\Persistence;


use Orm\Zed\TermsOfService\Persistence\SpyMerchantToTermsOfServiceQuery;
use Orm\Zed\TermsOfService\Persistence\SpyTermsOfServiceQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class TermsOfServicePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return SpyTermsOfServiceQuery
     */
    public function createTermsOfServiceQuery()
    {
        return SpyTermsOfServiceQuery::create();
    }

    /**
     * @return \Orm\Zed\TermsOfService\Persistence\SpyMerchantToTermsOfServiceQuery
     */
    public function createMerchantToTermsOfServiceQuery()
    {
        return SpyMerchantToTermsOfServiceQuery::create();
    }
}