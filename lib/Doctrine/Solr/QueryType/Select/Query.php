<?php

namespace Doctrine\Solr\QueryType\Select;

use Doctrine\Solr\Converter\Converter;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\ClassMetadataFactory;
use Doctrine\Solr\Configuration;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

class Query extends SelectQuery
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var DocumentMetadata
     */
    protected $documentMetadata;

    /**
     * @var Converter
     */
    protected $converter;

    protected function init()
    {
        $this->config = $this->options['config'];
        $this->documentMetadata = $this->config->getClassMetadataFactory()->getMetadataFor($this->options['mappedDocument']);
        $this->converter = $this->config->getConverter();
        parent::init();
    }

    /**
     * Returns Converter associated with this Query.
     *
     * @return \Doctrine\Solr\Converter\Converter
     */
    public function getConverter() {
        return $this->converter;
    }

    public function getResponseParser() {
        $converter = $this->converter;
        $documentClass = $this->getOption('mappedDocument');
        return new ResponseParser(function ($document) use ($converter, $documentClass) {
            return $converter->fromSolrDocument($document, $documentClass);
        });
    }

    /**
     * Sets query to match passed $document.
     * @param $document
     * @param {boolean} $toSolrDocument if set to false the document won't be converted
     */
    public function setQueryByDocument($document, $toSolrDocument = true) {
        return parent::setQuery($this->converter->toQuery($document, $toSolrDocument));
    }

    public function addField($field)
    {
        return parent::addField($this->documentMetadata->getSolrFieldName($field));
    }

    public function removeField($field)
    {
        return parent::removeField($this->documentMetadata->getSolrFieldName($field));
    }

    public function addSort($sort, $order)
    {
        return parent::addSort($this->documentMetadata->getSolrFieldName($sort),$order);
    }

    public function removeSort($sort)
    {
        return parent::removeSort($this->documentMetadata->getSolrFieldName($sort));
    }
}
