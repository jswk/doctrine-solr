<?php
namespace Doctrine\Solr\Converter;
use Solarium\QueryType\Update\Query\Document;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\ClassMetadataFactory;

class SolrDocumentConverter implements Converter
{
    /** @var \Doctrine\Solr\Metadata\ClassMetadataFactory */
    private $cmf;

    public function __construct(ClassMetadataFactory $cmf)
    {
        $this->cmf = $cmf;
    }

    /**
     * Returns converted $document
     *
     * @param Object $document with direct access to fields i.e. $document->field
     * @return \Solarium\QueryType\Update\Query\Document
     */
    public function toSolrDocument($document)
    {
        /** @var $metadata DocumentMetadata */
        $metadata = $this->cmf->getMetadataFor(get_class($document));

        $converted = new Document();

        foreach ($metadata->getFieldNames() as $fieldName) {
            $converted
                    ->addField($metadata->getSolrFieldName($fieldName),
                            $document->$fieldName);
        }

        return $converted;
    }

    public function toDocument($document)
    {
        // TODO: Auto-generated method stub

    }

    /**
     * Converts $document to query matching all its fields.
     *
     * @param $document
     * @return string
     */
    public function toQuery($document)
    {
        $query = array();
        foreach ($document->getIterator() as $key => $value) {
            $query[] = "(${key}:\"${value}\")";
        }
        return implode('AND', $query);
    }
}
