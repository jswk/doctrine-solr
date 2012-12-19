<?php
namespace Doctrine\Solr\Converter;
use Doctrine\Solr\Configuration;

use Solarium\QueryType\Select\Result\AbstractDocument;

use Solarium\QueryType\Update\Query\Document;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\ClassMetadataFactory;

class DocumentConverter implements Converter
{
    /** @var \Doctrine\Solr\Metadata\ClassMetadataFactory */
    private $cmf;

    public function __construct(Configuration $config)
    {
        $this->cmf = $config->getClassMetadataFactory();
    }

    public function toSolrDocument($document)
    {
        /** @var $metadata DocumentMetadata */
        $metadata = $this->cmf->getMetadataFor(get_class($document));

        $converted = new Document();

        foreach ($metadata->getFieldNames() as $fieldName) {
            $converted->addField($metadata->getSolrFieldName($fieldName), $document->$fieldName);
        }

        return $converted;
    }

    public function fromSolrDocument($document, $class)
    {
        /** @var DocumentMetadata $metadata */
        $metadata = $this->cmf->getMetadataFor($class);

        $converted = new $class();

        $map = $metadata->getSolrToStandardFieldNameMapping();

        foreach ($document->getFields() as $field => $value) {
            $converted->{$map[$field]} = $value;
        }

        return $converted;
    }

    public function toQuery($document, $toSolrDocument = false)
    {
        if ($toSolrDocument) {
            $document = $this->toSolrDocument($document);
        }
        $query = array();
        foreach ($document->getIterator() as $key => $value) {
            $query[] = "(${key}:\"${value}\")";
        }
        return implode('AND', $query);
    }
}
