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
    public function toDocument($document);
    public function toQuery($document);
}
