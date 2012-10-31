<?php
namespace Doctrine\Solr\Persistence;

use \Solarium_Client;

/**
 * Provides methods to modify Solr database.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 */
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
     * Returns Solarium_Client object
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
     * Holds config
     *
     * @var array
     */
    protected $config;

    /**
     * Returns config
     *
     * @return array
     */
    protected function getConfig()
    {
        if ($this->config == null) {
            $this->config = array(
                'adapteroptions' => array(
                    'host' => '127.0.0.1',
                    'port' => 8983,
                    'path' => '/solr/',
                )
            );
            // TODO: implement config reading
        }
        return $this->config;
    }

    /**
     *
     * @return \Doctrine\Solr\Persistence\SolrPersister
     */
    protected function setConfig(array $config, $override = false)
    {
        if (!$override) {
            $this->config = array_merge($config, $this->getConfig());
        } else {
            $this->config = array_merge($this->getConfig(), $config);
        }
        return $this->getConfig();
    }

    /**
     * Sets proper config and Client on demand
     *
     * @param array $config
     * @param \Solarium_Client $client
     */
    public function __construct(array $config = null, \Solarium_Client $client = null)
    {
        $this->setConfig((array) $config, true);
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(\Solarium_Document_ReadOnly $document)
    {
        // adds document to the list
        $this->insertUpdate[] = $document;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(\Solarium_Document_ReadOnly $document)
    {
        // adds document to the list
        $this->delete[] = $document;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function update(\Solarium_Document_ReadOnly $document)
    {
        // adds document to the list
        return $this->persist($document);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        // if nothing to commit, exit
        if ($this->insertUpdate == array() && $this->delete == array()) {
            return $this;
        }

        $client = $this->getSolariumClient();

        $update = $client->createUpdate();

        // if there's something to remove
        if ($this->delete != array()) {
            foreach ($this->delete as $doc) {
                $update->addDeleteQuery($this->documentToQuery($doc));
            }
        }

        // if there's something to insert/update
        if ($this->insertUpdate != array()) {
            $update->addDocuments($this->insertUpdate);
        }

        // commit changes to the database, otherwise changes
        // would wait to be commited
        $update->addCommit();

        // run the update
        $result = $client->update($update);

        // something went wrong
        if ($result->getStatus() != 0) {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Convert document to delete query
     *
     * @param \Solarium_Document_ReadOnly document to be converted
     * @return string query (i.e. '(id:"15*")AND(title:"Moby Dick")')
     */
    private function documentToQuery(\Solarium_Document_ReadOnly $document)
    {
        $query = array();
        foreach ($document->getIterator() as $key => $value) {
            $query[] = "(${key}:\"${value}\")";
        }
        return implode('AND', $query);
    }
}
