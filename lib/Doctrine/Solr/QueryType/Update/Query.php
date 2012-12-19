<?php
namespace Doctrine\Solr\QueryType\Update;

use Solarium\QueryType\Update\Query\Command\Delete;

use Doctrine\Solr\Converter\Converter;

use Solarium\QueryType\Update\Query\Query as UpdateQuery;

class Query extends UpdateQuery
{
    /** @var \Doctrine\Solr\Converter\DocumentConverter */
    protected $converter;

    protected function init()
    {
        parent::init();
        $this->config = $this->options['config'];
        $this->converter = $this->config->getConverterImpl();
    }

    /**
     * Adds documents to be commited.
     * They are additionally converted to SolrDocument.
     *
     * @param $document
     * @param boolean $overwrite
     * @param int $commitWithin
     */
    public function addDocuments($documents, $overwrite = null, $commitWithin = null)
    {
        $convertedDocs = [];
        foreach ($documents as $document) {
            $convertedDocs[] = $this->converter->toSolrDocument($document);
        }

        return parent::addDocuments($convertedDocs, $overwrite, $commitWithin);
    }

    /**
     *
     * @param $document
     * @return \Doctrine\Solr\QueryType\Update\Query
     */
    public function removeDocument($document)
    {
        return $this->removeDocuments([$document]);
    }

    /**
     *
     * @param array $documents
     * @return \Doctrine\Solr\QueryType\Update\Query
     */
    public function removeDocuments(array $documents)
    {
        $queries = [];
        foreach ($documents as $document) {
            $queries[] = $this->converter->toQuery($document);
        }

        return $this->addDeleteQueries($queries);
    }
}
