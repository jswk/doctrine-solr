<?php

namespace Doctrine\Solr\Tests\Persistence;

use Doctrine\Solr\Persistence\SolrPersister;
use \PHPUnit_Framework_TestCase;

class SolrPersisterTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $update;

    public function setUp()
    {
        $this->update = $this->getMock('\Solarium_Query_Update');

        $this->client = $this->getMock('\Solarium_Client');
    }

    public function testPersistUpdateRemove()
    {
        $add = new \Solarium_Document_ReadOnly(
            array(
                'fieldOne' => 'two',
                'fieldTwo' => 'seven',
            )
        );
        $update = new \Solarium_Document_ReadOnly(
            array(
                'fieldUp' => 'two',
                'fieldDown' => 'seven',
            )
        );
        $remove = new \Solarium_Document_ReadOnly(
            array(
                'id' => 'two',
                'fieldTwo' => 'seven',
            )
        );
        $result = $this->getMock('\Solarium_Result_Update', array(), array(), '', false);

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

        $persister = new SolrPersister(array(), $this->client);

        $persister->persist($add)
                  ->update($update)
                  ->remove($remove)
                  ->flush();
    }

    public function testMethodsReturnSelf()
    {
        $persister = new SolrPersister();
        $this->assertEquals($persister, $persister->flush());
    }
}
