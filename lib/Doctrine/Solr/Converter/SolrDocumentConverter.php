<?php
namespace Doctrine\Solr\Converter;

use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\ClassMetadataFactory;
use Solarium_Document_ReadWrite;

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
     * @return \Solarium_Document_ReadOnly
     */
    public function getConverted($document)
    {
        /** @var $metadata DocumentMetadata */
        $metadata = $this->cmf->getMetadataFor(get_class($document));

        $converted = new Solarium_Document_ReadWrite();

        foreach ($metadata->getFieldNames() as $fieldName) {
            $converted->addField($metadata->getSolrFieldName($fieldName), $document->$fieldName);
        }

        return $converted;
    }
}
