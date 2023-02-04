<?php

namespace Pyz\Zed\DocumentationGeneratorRestApi\Communication\Console;

use Spryker\Zed\DocumentationGeneratorRestApi\Business\DocumentationGeneratorRestApiFacadeInterface;
use Spryker\Zed\DocumentationGeneratorRestApi\Communication\Console\GenerateRestApiDocumentationConsole as SprykerGenerateRestApiDocumentationConsole;
use Spryker\Zed\DocumentationGeneratorRestApi\Communication\DocumentationGeneratorRestApiCommunicationFactory;

/**
 * @method DocumentationGeneratorRestApiFacadeInterface getFacade()
 * @method DocumentationGeneratorRestApiCommunicationFactory getFactory()
 */
class GenerateRestApiDocumentationConsole extends SprykerGenerateRestApiDocumentationConsole
{
    /**
     * @deprecated Will be removed without replacement.
     *
     * @return bool
     */
    protected function isDocumentationGenerationEnabled(): bool
    {
        return in_array(
            APPLICATION_ENV,
            [
                'development',
                'devtest',
                'docker.dev',
                'docker.devtest',
                'docker.ci'
            ],
            true
        );
    }
}
