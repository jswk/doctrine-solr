<?php

namespace Doctrine\Solr\Tests\Persistence;

use Solarium\QueryType\Select\Result\Document;

use Doctrine\Solr\Persistence\SolrPersister;

use \PHPUnit_Framework_TestCase;

class SolrPersisterTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $update;
    protected $config;

    public function setUp()
    {
        $this->update = $this->getMock('Solarium\\QueryType\\Update\\Query\\Query');

        $this->client = $this->getMock('Solarium\\Client');

        $this->config = $this->getMock('Doctrine\\Solr\\Configuration', [], [], '', false);
        $this->config->expects($this->any())
                     ->method('getSolariumClientImpl')
                     ->will($this->returnValue($this->client));
    }

    public function testPersistUpdateRemove()
    {
        $add = new Document(
            array(
                'fieldOne' => 'two',
                'fieldTwo' => 'seven',
            )
        );
        $update = new Document(
            array(
                'fieldUp' => 'two',
                'fieldDown' => 'seven',
            )
        );
        $remove = new Document(
            array(
                'id' => 'two',
                'fieldTwo' => 'seven',
            )
        );

        $result = $this->getMock('Solarium\\QueryType\\Update\\Result', array(), array(), '', false);

        $this->client->expects($this->once())
                     ->method('createUpdate')
                     ->will($this->returnValue($this->update));

        $this->update->expects($this->once())
                     ->method('addDocuments')
                     ->with(array($add, $update));

        $this->update->expects($this->once())
                     ->method('addDeleteQuery')
                     ->with('(id:"' . $remove->id . '")AND(fieldTwo:"' . $remove->fieldTwo . '")');

        $this->update->expects($this->once())
                     ->method('addCommit');

        $this->client->expects($this->once())
                     ->method('update')
                     ->with($this->update)
                     ->will($this->returnValue($result));

        $result->expects($this->once())
               ->method('getStatus')
               ->will($this->returnValue(0));

        $persister = new SolrPersister($this->config);

        $persister->persist($add)
                  ->update($update)
                  ->remove($remove)
                  ->flush();
    }

    public function testMethodsReturnSelf()
    {
        $persister = new SolrPersister($this->config);
        $this->assertEquals($persister, $persister->flush());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testFlushThrowsUnexpectedValueExceptionOnClientError()
    {
        $add = new Document(
            array(
                'fieldOne' => 'two',
                'fieldTwo' => 'seven',
            )
        );
        $result = $this->getMock('Solarium\\QueryType\\Update\\Result', array(), array(), '', false);

        $this->client->expects($this->once())
                     ->method('createUpdate')
                     ->will($this->returnValue($this->update));

        $this->update->expects($this->once())
                     ->method('addCommit');

        $this->client->expects($this->once())
                     ->method('update')
                     ->with($this->update)
                     ->will($this->returnValue($result));

        $result->expects($this->any())
               ->method('getStatus')
               ->will($this->returnValue(1));

        $persister = new SolrPersister($this->config);

        $persister->persist($add)
                  ->flush();
    }
}
