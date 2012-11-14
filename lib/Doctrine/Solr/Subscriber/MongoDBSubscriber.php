<?php

namespace Doctrine\Solr\Subscriber;

use \Doctrine\Common\EventSubscriber;
use \Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use \Doctrine\ODM\MongoDB\Events;
use \Doctrine\ODM\MongoDB\DocumentManager;
use \Doctrine\Solr\Persistence\Persister;
use \Doctrine\Solr\Metadata\ClassMetadataFactory;
use \Solarium_Document_ReadWrite as SolrDocument;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class MongoDBSubscriber implements EventSubscriber
{
    /** @var \Doctrine\Solr\Persistence\Persister */
    private final $persister;

    /** @var \Doctrine\Solr\Converter\Converter */
    private final $converter;

    /**
     *
     * @param Persister $persister
     */
    public function __construct(Persister $persister, Converter $converter)
    {
        $this->persister = $persister;
        $this->converter = $converter;
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

    /**
     * Fetches a ClassMetadataFactory associated with the object.
     *
     * @return \Doctrine\Solr\Metadata\ClassMetadataFactory
     */
    protected function getClassMetadataFactory()
    {
        return $this->cmf;
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
        $this->getPersister()->flush();
    }

    /**
     * Persists document, converting it to Solr_Document.
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $solrDocument = $this->converter->getConverted($document);
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
