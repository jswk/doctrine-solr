<?php

namespace Doctrine\Solr\Subscriber;

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
    /** @var \Doctrine\Solr\Persistence\Persister */
    private $persister;

    /** @var \Doctrine\Solr\Converter\Converter */
    private $converter;

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
     * Fetches a converter
     *
     * @return \Doctrine\Solr\Converter\Converter
     */
    protected function getConverter()
    {
        return $this->converter;
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
        $document = $this->convert($eventArgs->getDocument());
        $this->getPersister()->persist($document);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $this->convert($eventArgs->getDocument());
        $this->getPersister()->update($document);
    }

    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $document = $this->convert($eventArgs->getDocument());
        $this->getPersister()->remove($document);
    }

    /**
     *
     * @param Object $document
     * @return converted object
     */
    private function convert($document)
    {
        return $this->getConverter()->getConverted($document);
    }
}
