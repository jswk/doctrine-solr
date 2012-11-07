<?php
namespace Doctrine\Solr\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Solr\Metadata\ClassMetadata;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\Driver\AnnotationDriver;

use PHPUnit_Framework_TestCase;

class AnnotationDriverTest extends PHPUnit_Framework_TestCase
{
    private $driver;

    private $className = 'Doctrine\Solr\Tests\Mapping\Document1';

    public function setUp()
    {
        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);
    }

    public function testLoadMetadata()
    {
        $metadata = new DocumentMetadata($this->className);

        $this->driver->loadMetadataForClass($this->className, $metadata);

        $this->assertEquals('test', $metadata->getCollection());

        $this->assertTrue($metadata->hasField('id'));
        $this->assertFalse($metadata->hasField('notImportant'));

        $this->assertTrue($metadata->isUniqueKey('id'));

        $this->assertEquals('string', $metadata->getTypeOfField('id'));
        $this->assertEquals('string', $metadata->getTypeOfField('important'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadMetadataThrowsExceptionIfClassMetadataNotSupported()
    {
        $this->driver->loadMetadataForClass($this->className, new UnsupportedClassMetadata());
    }
}

class UnsupportedClassMetadata implements ClassMetadata
{
    public function getName()
    {
    }
    public function getReflectionClass()
    {
    }
    public function hasField($fieldName)
    {
    }
    public function getFieldNames()
    {
    }
    public function getSolrFieldName($fieldName)
    {
    }
    public function getTypeOfField($fieldName)
    {
    }
    public function isUniqueKey($fieldName)
    {
    }
}
