<?php
namespace Doctrine\Solr\Converter;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 */
interface Converter
{
    public function toSolrDocument($document);
    public function fromSolrDocument($document, $class);
    public function toQuery($document);
}
