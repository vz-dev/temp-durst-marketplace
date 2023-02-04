<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Product\Business;

use Pyz\Zed\Product\Business\Hydrator\ProductNameHydrator;
use Pyz\Zed\Product\Business\Manager\GtinManager;
use Pyz\Zed\Product\Business\Mapper\ProductExporterMapper;
use Pyz\Zed\Product\Business\Product\NameGenerator\ProductAbstractNameGenerator;
use Pyz\Zed\Product\Business\Product\NameGenerator\ProductConcreteNameGenerator;
use Pyz\Zed\Product\Business\Product\ProductUrlGenerator;
use Spryker\Zed\Product\Business\ProductBusinessFactory as SprykerProductBusinessFactory;

class ProductBusinessFactory extends SprykerProductBusinessFactory
{
    /**
     * @return \Spryker\Zed\Product\Business\Product\Url\ProductUrlGenerator
     */
    public function createProductUrlGenerator()
    {
        return new ProductUrlGenerator(
            $this->createProductAbstractNameGenerator(),
            $this->getLocaleFacade(),
            $this->getUtilTextService()
        );
    }

    /**
     * @return ProductNameHydrator
     */
    public function createProductNameHydrator()
    {
        return new ProductNameHydrator(
            $this->getQueryContainer()
        );
    }

    /**
     * @return GtinManager
     */
    public function createGtinManager()
    {
        return new GtinManager(
            $this->getQueryContainer()
        );
    }

    /**
     * @return ProductExporterMapper
     */
    public function createProductExporterMapper()
    {
        return new ProductExporterMapper();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Product\NameGenerator\ProductConcreteNameGeneratorInterface
     */
    public function createProductConcreteNameGenerator()
    {
        return new ProductConcreteNameGenerator();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Product\NameGenerator\ProductAbstractNameGeneratorInterface
     */
    public function createProductAbstractNameGenerator()
    {
        return new ProductAbstractNameGenerator();
    }
}
