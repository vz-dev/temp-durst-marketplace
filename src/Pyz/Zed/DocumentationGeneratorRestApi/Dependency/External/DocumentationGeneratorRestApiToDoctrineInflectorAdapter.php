<?php
/**
 * Durst - project - DocumentationGeneratorRestApiToDoctrineInflectorAdapter.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.11.21
 * Time: 13:26
 */

namespace Pyz\Zed\DocumentationGeneratorRestApi\Dependency\External;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Spryker\Zed\DocumentationGeneratorRestApi\Dependency\External\DocumentationGeneratorRestApiToDoctrineInflectorAdapter as SprykerDocumentationGeneratorRestApiToDoctrineInflectorAdapter;

class DocumentationGeneratorRestApiToDoctrineInflectorAdapter extends SprykerDocumentationGeneratorRestApiToDoctrineInflectorAdapter
{
    /**
     * {@inheritDoc}
     *
     * @param string $word
     * @return string
     */
    public function classify(string $word): string
    {
        $inflector = InflectorFactory::create()
            ->build();

        return $inflector->classify($word);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $word
     * @return string
     */
    public function singularize(string $word): string
    {

        $inflector = InflectorFactory::create()
            ->build();

        return $inflector->singularize($word);
    }

}
