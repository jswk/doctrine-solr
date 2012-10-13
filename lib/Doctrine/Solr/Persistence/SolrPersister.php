<?php
namespace Doctrine\Solr\Persistence;

use \Solarium_Client;

class SolrPersister implements Persister
{
    /**
     * Holds information about changes and inserts to the Solr.
     * Solr implements upsert, so one property is sufficient.
     *
     * @var array<\Solarium_Document_ReadOnly>
     */
    protected $insertUpdate = array();

    /**
     * Holds information about documents to be deleted
     *
     * @var array<\Solarium_Document_ReadOnly>
    */
    protected $delete = array();

    /**
     * @var \Solarium_Client
     */
    protected $client;

    /**
     *
     * @return \Solarium_Client
     */
    protected function getSolariumClient()
    {
        if ($this->client == null) {
            $this->client = new Solarium_Client($this->getConfig());
        }
        return $this->client;
    }
    /**
     * @var array
     */
    protected $config;

    /**
     *
     * @return array
     */
    protected function getConfig()
    {
        if ($this->config == null) {
            $this->config = array(); // TODO: implement config reading
        }
        return $this->config;
    }

    /**
     *
     * @return \Doctrine\Solr\Persistence\SolrPersister
     */
    public function setConfig(array $config, $override = false)
    {
        if ($override) {
            $this->config = $config;
        } else {
            $this->config = array_merge($this->config, $config);
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\Solr\Persistence\Persister::persist()
     */
    public function persist(\Solarium_Document_ReadOnly $document)
    {
        $this->insertUpdate[] = $document;
        return $this;
    }

    public function remove(\Solarium_Document_ReadOnly $document)
    {
        $this->remove[] = $document;
        return $this;
    }

    public function update(\Solarium_Document_ReadOnly $document)
    {
        $this->persist($document);
        return $this;
    }

    public function flush()
    {
        $client = $this->getSolariumClient();
        if ($this->remove != array()) {
            $update = $client->createUpdate();
            $update->addDocuments($this->insertUpdate);
            // FIXME: finish removing documents
        }
        if ($this->insertUpdate != array()) {
            $update = $client->createUpdate();
            $update->addDocuments($this->insertUpdate);
            $update->addCommit();
            $client->update($update);
        }

    }
}
