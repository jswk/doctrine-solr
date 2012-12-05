<?php

namespace Doctrine\Solr\Subscriber;
use Doctrine\Solr\QueryType\Update\Query;

use Solarium\Client;

use \Doctrine\Common\EventSubscriber;
use \Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use \Doctrine\ODM\MongoDB\Events;
use \Doctrine\ODM\MongoDB\DocumentManager;
use \Doctrine\Solr\Persistence\Persister;
use \Doctrine\Solr\Metadata\ClassMetadataFactory;
use \Doctrine\Solr\Converter\Converter;

/**
 * Connects MongoDB and Solr.
 * Should be added as an EventManager's subscriber.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class MongoDBSubscriber implements EventSubscriber
{
    /** @var \Solarium\Client */
    private $client;

    /** @var \Solarium\QueryType\Update\Query\Query */
    private $query;

    /**
     *
     * @param Client $client must have its queryTypes updated
     * @param Converter $converter
     * @param string $updateOptions
     */
    public function __construct(Client $client, Converter $converter)
    {
        $this->client = $client;
        $this->query = $client->createUpdate([
            'converter' => $converter,
        ]);
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::postFlush,
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate
        );
    }

    /**
     * Flushes data added, deleted or updated during other events
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        $this->client->execute($this->query);
    }

    /**
     * Persists document, converting it to Solr_Document.
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->query->addDocument($eventArgs->getDocument());
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->query->addDocument($eventArgs->getDocument(), true);
    }

    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $this->query->removeDocument($eventArgs->getDocument());
    }
}
