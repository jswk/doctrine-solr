<?php
namespace Doctrine\Solr\Persistence;

use Solarium\QueryType\Select\Result\AbstractDocument;
use Solarium\Client;

use Doctrine\Solr\Configuration;

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
     * @var array<AbstractDocument>
     */
    protected $insertUpdate = array();

    /**
     * Holds information about documents to be deleted
     *
     * @var array<AbstractDocument>
    */
    protected $delete = array();

    /**
     * @var \Solarium\Client
     */
    protected $client;

    /**
     * Holds config
     *
     * @var Doctrine\Solr\Configuration
     */
    protected $config;

    /**
     * Returns config
     *
     * @return Configuration
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns Solarium\Client object
     *
     * @return \Solarium\Client
     */
    protected function getSolariumClient()
    {
        if ($this->client == null) {
            $this->client = $this->getConfig()->getSolariumClientImpl();
        }
        return $this->client;
    }

    /**
     * Sets proper config and Client on demand
     *
     * @param array $config
     * @param \Solarium\Client $client
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(AbstractDocument $document)
    {
        // adds document to the list
        $this->insertUpdate[] = $document;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(AbstractDocument $document)
    {
        // adds document to the list
        $this->delete[] = $document;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function update(AbstractDocument $document)
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
                $update->addDeleteQuery(self::documentToQuery($doc));
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
            throw new \UnexpectedValueException("Solarium Client returned error: " . $result->getStatus());
        }

        return $this;
    }

    /**
     * Convert document to delete query
     *
     * @param AbstractDocument document to be converted
     * @return string query (i.e. '(id:"15*")AND(title:"Moby Dick")')
     */
    private static function documentToQuery(AbstractDocument $document)
    {
        $query = array();
        foreach ($document->getIterator() as $key => $value) {
            $query[] = "(${key}:\"${value}\")";
        }
        return implode('AND', $query);
    }
}
