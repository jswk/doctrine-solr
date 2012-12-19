<?php
namespace Doctrine\Solr\Converter;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 */
interface Converter
{
    /**
     * Returns converted $document
     *
     * @param Object $document with direct access to fields i.e. $document->field
     * @return \Solarium\QueryType\Update\Query\Document
     */
    public function toSolrDocument($document);

    /**
     * Returns converted $document
     *
     * @param AbstractDocument $document
     * @return \Solarium\QueryType\Update\Query\Document
     */
    public function fromSolrDocument($document, $class);

    /**
     * Converts $document to query matching all its fields.
     *
     * @param $document Document
     * @return string
     */
    public function toQuery($document, $toSolrDocument);
}
