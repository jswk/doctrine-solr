<?php
namespace Doctrine\Solr\Tests\Subscriber;

use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Solr\Subscriber\MongoDBSubscriber;
use Solarium_Document_ReadOnly;
use PHPUnit_Framework_TestCase;

class MongoDBSubscriberTest extends PHPUnit_Framework_TestCase
{
    public $persister;

    public $converter;

    public $subscriber;

    public function setUp()
    {
        $this->persister = $this->getMock('Doctrine\\Solr\\Persistence\\SolrPersister', [], [], '', false);
        $this->converter = $this->getMock('Doctrine\\Solr\\Converter\\SolrDocumentConverter', [], [], '', false);
        $this->subscriber = new MongoDBSubscriber($this->persister, $this->converter);
    }

    public function tearDown()
    {
        $this->persister = null;
        $this->converter = null;
        $this->subscriber = null;
    }

    public function testPersist()
    {
        $this->converter->expects($this->any())
                        ->method('getConverted')
                        ->will($this->returnValue(new Solarium_Document_ReadOnly([])));

        $this->persister->expects($this->once())
                        ->method('persist');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postPersist($eventArgs);
    }

    public function testUpdate()
    {
        $this->converter->expects($this->any())
                        ->method('getConverted')
                        ->will($this->returnValue(new Solarium_Document_ReadOnly([])));

        $this->persister->expects($this->once())
                        ->method('update');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postUpdate($eventArgs);
    }

    public function testRemove()
    {
        $this->converter->expects($this->any())
                        ->method('getConverted')
                        ->will($this->returnValue(new Solarium_Document_ReadOnly([])));

        $this->persister->expects($this->once())
                        ->method('remove');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postRemove($eventArgs);
    }

    public function testFlush()
    {
        $this->persister->expects($this->once())
                        ->method('flush');

        $eventArgs = new MockPostFlushEventArgs();

        $this->subscriber->postFlush($eventArgs);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertCount(4, $this->subscriber->getSubscribedEvents());
    }
}

// define it because original constructor needed DocumentManager passed which isn't used in this implementation
class MockPostFlushEventArgs extends PostFlushEventArgs
{
    public function __construct()
    {
    }
}
