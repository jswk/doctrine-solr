<?php
namespace Doctrine\Solr\Persistence;

use Solarium\QueryType\Select\Result\AbstractDocument;

interface Persister
{
    /**
     * Records information about document to be persisted
     *
     * @param AbstractDocument $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function persist(AbstractDocument $document);

    /**
     * Records information about document to be removed
     *
     * @param AbstractDocument $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function remove(AbstractDocument $document);

    /**
     * Records information about document to be updated
     *
     * @param AbstractDocument $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function update(AbstractDocument $document);

    /**
     * Saves the information to the database.
     * Must be called to actually write to the database.
     *
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function flush();
}
