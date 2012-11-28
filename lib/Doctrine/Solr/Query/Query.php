<?php
namespace Doctrine\Solr\Query;

use Solarium\QueryType\Select\Result\Document;

/**
 * Object used to store, execute and retrieve results of an Expr.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class Query
{
    /**
     * Executes the query and returns the result array.
     * @return \ArrayIterator<\Solarium\QueryType\Select\Result\Document>
     */
    public function execute()
    {
        $result = array();

        return new \ArrayIterator($result); // FIXME: add implementation
    }

    /**
     * Executes the query and returns single result.
     * @return \Solarium\QueryType\Select\Result\Document
     */
    public function getSingleResult()
    {
        return $this->execute()[0];
    }
}
