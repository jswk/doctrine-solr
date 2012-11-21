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
     * Returns converted object.
     *
     * @param $document
     * @return converted object
     */
    public function getConverted($document);
}
