<?php
namespace Doctrine\Solr\Persistence;

interface Persister
{
    /**
     * Records information about document to be persisted
     *
     * @param \Solarium_Document_ReadOnly $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function persist(\Solarium_Document_ReadOnly $document);

    /**
     * Records information about document to be removed
     *
     * @param \Solarium_Document_ReadOnly $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function remove(\Solarium_Document_ReadOnly $document);

    /**
     * Records information about document to be updated
     *
     * @param \Solarium_Document_ReadOnly $document
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function update(\Solarium_Document_ReadOnly $document);

    /**
     * Saves the information to the database.
     * Must be called to actually write to the database.
     *
     * @return \Doctrine\Solr\Persistence\Persister
     */
    public function flush();
}
