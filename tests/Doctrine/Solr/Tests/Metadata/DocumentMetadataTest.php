<?php

namespace Doctrine\Solr\Tests\Metadata;

use Doctrine\Solr\Metadata\DocumentMetadata;
use PHPUnit_Framework_TestCase;

class DocumentMetadataTest extends PHPUnit_Framework_TestCase
{
    private $documentMetadata;
    private $name = 'Doctrine\Solr\Tests\Mapping\Document1';

    public function setUp()
    {
        $this->documentMetadata = new DocumentMetadata($this->name);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfNoTypeSpecified()
    {
        $this->documentMetadata->addField(['name' => 'field']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfIncorrectTypeSpecified()
    {
        $this->documentMetadata->addField(['name' => 'field', 'type' => 'incorrectType']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfNoFieldNameSpecified()
    {
        $this->documentMetadata->addField(['type' => 'string']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfTryingToEditField()
    {
        $this->documentMetadata->addField(['name' => 'field', 'type' => 'string']);
        $this->documentMetadata->addField(['name' => 'field', 'type' => 'int']);
    }

    public function testSetsAndReturnsName()
    {
        $this->assertEquals($this->name, $this->documentMetadata->getName());
    }

    public function testReturnsReflectionClass()
    {
        $this->assertInstanceOf('ReflectionClass', $this->documentMetadata->getReflectionClass());
    }

    public function testHasField()
    {
        $this->documentMetadata->addField(['name' => 'testField', 'type' => 'string']);
        $this->assertTrue($this->documentMetadata->hasField('testField'));
        $this->assertFalse($this->documentMetadata->hasField('nonexistent'));
    }

    public function testGetFieldNames()
    {
        $this->documentMetadata->addField(['name' => 'name1', 'type' => 'string']);
        $this->documentMetadata->addField(['name' => 'name2', 'type' => 'string']);
        $tmp = $this->documentMetadata->getFieldNames();
        sort($tmp);
        $this->assertEquals(['name1', 'name2'], $tmp);
    }

    public function testSolrFieldName()
    {
        $this->documentMetadata->addField(['name' => 'name1', 'type' => 'string']);

        $this->assertEquals('name1_s', $this->documentMetadata->getSolrFieldName('name1'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSolrFieldNameThrowsExceptionIfFieldNonExistent()
    {
        $this->documentMetadata->getSolrFieldName('name');
    }

    public function testGetTypeOfField()
    {
        $this->documentMetadata->addField(['name' => 'name1', 'type' => 'int']);
        $this->assertEquals('int', $this->documentMetadata->getTypeOfField('name1'));
    }

    public function testIsUniqueKey()
    {
        $this->documentMetadata->addField(['name' => 'name1', 'type' => 'int', 'uniqueKey' => true]);
        $this->documentMetadata->addField(['name' => 'name2', 'type' => 'int', 'uniqueKey' => false]);
        $this->documentMetadata->addField(['name' => 'name3', 'type' => 'int']);

        $this->assertTrue($this->documentMetadata->isUniqueKey('name1'));
        $this->assertFalse($this->documentMetadata->isUniqueKey('name2'));
        $this->assertFalse($this->documentMetadata->isUniqueKey('name3'));
    }
}
