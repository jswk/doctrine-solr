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
        parent::init();
        $this->config = $this->options['config'];
        //$this->converter = $this->config->getConverterImpl();
        $this->documentMetadata = $this->config->getClassMetadataFactory()->getMetadataFor($this->options['mappedDocument']);
        $this->converter = $this->config->getConverterImpl();
    }

    public function setQueryByDocument($document, $toSolrDocument = true) {
        parent::setQuery($this->converter->toQuery($document, $toSolrDocument));
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
