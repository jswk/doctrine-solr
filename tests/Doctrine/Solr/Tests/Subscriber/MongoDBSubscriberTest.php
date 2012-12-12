<?php
namespace Doctrine\Solr\Tests\Subscriber;

use Solarium\QueryType\Select\Result\Document;

use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Solr\Subscriber\MongoDBSubscriber;

use PHPUnit_Framework_TestCase;

class MongoDBSubscriberTest extends PHPUnit_Framework_TestCase
{
    public $query;

    public $client;

    public $subscriber;

    public function setUp()
    {
        $this->query = $this->getMock('Doctrine\\Solr\\QueryType\\Update\\Query', [], [], '', false);
        $this->client = $this->getMock('Solarium\\Client', [], [], '', false);
        $converter = $this->getMock('Doctrine\\Solr\\Converter\\DocumentConverter', [], [], '', false);

        $this->client->expects($this->once())
                     ->method('createUpdate')
                     ->will($this->returnValue($this->query));

        // test subject
        $this->subscriber = new MongoDBSubscriber($this->client, $converter);
    }

    public function tearDown()
    {
        $this->query = null;
        $this->subscriber = null;
    }

    public function testPersist()
    {
        $this->query->expects($this->once())
                        ->method('addDocument');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postPersist($eventArgs);
    }

    public function testUpdate()
    {
        $this->query->expects($this->once())
                        ->method('addDocument');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postUpdate($eventArgs);
    }

    public function testRemove()
    {
        $this->query->expects($this->once())
                        ->method('removeDocument');

        $eventArgs = new LifecycleEventArgs(null, null);

        $this->subscriber->postRemove($eventArgs);
    }

    public function testFlush()
    {
        $this->client->expects($this->once())
                        ->method('execute')
                        ->with($this->query);

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
