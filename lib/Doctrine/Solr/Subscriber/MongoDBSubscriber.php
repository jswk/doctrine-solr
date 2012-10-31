<?php

namespace Doctrine\Solr\Subscriber;

use \Doctrine\Common\EventSubscriber;
use \Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use \Doctrine\ODM\MongoDB\Events;
use \Doctrine\ODM\MongoDB\DocumentManager;
use \Doctrine\Solr\Persistence\Persister;
use \Solarium_Document_ReadWrite as SolrDocument;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class MongoDBSubscriber implements EventSubscriber
{
    /**
     *
     * @var \Doctrine\Solr\Persistence\Persister
     */
    private $persister;

    /**
     *
     * @param Persister $persister
     */
    public function __construct(Persister $persister = null)
    {
        $this->persister = $persister;
    }

    /**
     * Fetches a persister
     *
     * @return \Doctrine\Solr\Persistence\Persister
     */
    protected function getPersister()
    {
        return $this->persister;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::postFlush,
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        );
    }

    /**
     * Flushes data added, deleted or updated during other events
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        /** @var Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $eventArgs->getDocumentManager();

        //$dm->
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $metadata = ''; // TODO: metadata
        // TODO: load metadata for class
        // TODO: translate it to a SolrDocument
        // TODO: save information in the Solr database
        $this->getPersister()->persist($solrDocument);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->getPersister()->update($document);
    }

    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $this->getPersister()->remove($document);
    }
}
