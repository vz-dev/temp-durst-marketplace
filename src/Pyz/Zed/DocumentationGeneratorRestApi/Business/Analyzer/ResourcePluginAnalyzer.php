<?php

namespace Pyz\Zed\DocumentationGeneratorRestApi\Business\Analyzer;

use Generated\Shared\Transfer\AnnotationTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Zed\DocumentationGeneratorRestApi\Business\Analyzer\ResourcePluginAnalyzer as SprykerResourcePluginAnalyzer;
use Symfony\Component\HttpFoundation\Request;

class ResourcePluginAnalyzer extends SprykerResourcePluginAnalyzer
{
    /**
     * @param ResourceRoutePluginInterface $plugin
     * @param string $resourcePath
     * @param AnnotationTransfer|null $annotationTransfer
     *
     * @return void
     */
    protected function processGetResourceByIdPath(ResourceRoutePluginInterface $plugin, string $resourcePath, ?AnnotationTransfer $annotationTransfer): void
    {
        if (!$this->resourceRouteCollection->has(Request::METHOD_GET)) {
            return;
        }

        $this->httpMethodProcessor->addGetResourceByIdPath(
            $plugin,
            $resourcePath,
            $this->resourceRouteCollection->get(Request::METHOD_GET)[static::KEY_IS_PROTECTED],
            $this->getResourceIdFromResourceType($plugin->getResourceType()),
            $annotationTransfer
        );
    }

    /**
     * @param ResourceRoutePluginInterface $plugin
     * @param string $resourcePath
     * @param AnnotationTransfer|null $annotationTransfer
     *
     * @return void
     */
    protected function processGetResourceCollectionPath(ResourceRoutePluginInterface $plugin, string $resourcePath, ?AnnotationTransfer $annotationTransfer): void
    {
        if (!$this->resourceRouteCollection->has(Request::METHOD_GET)) {
            return;
        }

        $this->httpMethodProcessor->addGetResourceCollectionPath(
            $plugin,
            $resourcePath,
            $this->resourceRouteCollection->get(Request::METHOD_GET)[static::KEY_IS_PROTECTED],
            $this->getResourceIdFromResourceType($plugin->getResourceType()),
            $annotationTransfer
        );
    }
}
