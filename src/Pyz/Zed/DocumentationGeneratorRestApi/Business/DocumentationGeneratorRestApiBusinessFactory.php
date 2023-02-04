<?php

namespace Pyz\Zed\DocumentationGeneratorRestApi\Business;

use Pyz\Zed\DocumentationGeneratorRestApi\Business\Analyzer\ResourcePluginAnalyzer;
use Spryker\Zed\DocumentationGeneratorRestApi\Business\Analyzer\ResourcePluginAnalyzerInterface;
use Spryker\Zed\DocumentationGeneratorRestApi\Business\DocumentationGeneratorRestApiBusinessFactory as SprykerDocumentationGeneratorRestApiBusinessFactory;
use Spryker\Zed\DocumentationGeneratorRestApi\DocumentationGeneratorRestApiConfig;

/**
 * @method DocumentationGeneratorRestApiConfig getConfig()
 */
class DocumentationGeneratorRestApiBusinessFactory extends SprykerDocumentationGeneratorRestApiBusinessFactory
{
    /**
     * @return ResourcePluginAnalyzerInterface
     */
    public function createResourcePluginAnalyzer(): ResourcePluginAnalyzerInterface
    {
        return new ResourcePluginAnalyzer(
            $this->createRestApiMethodProcessor(),
            $this->getResourceRoutesPluginProviderPlugins(),
            $this->createGlueAnnotationAnalyzer(),
            $this->getTextInflector()
        );
    }
}
