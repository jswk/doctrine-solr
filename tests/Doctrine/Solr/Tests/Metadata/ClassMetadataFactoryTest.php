<?php
namespace Doctrine\Solr\Tests\Metadata;

use Doctrine\Solr\Configuration;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Tests\Mock\MappingDriverMock;
use PHPUnit_Framework_TestCase;
use Doctrine\Solr\Metadata\ClassMetadataFactory;

class ClassMetadataFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testClassMetadataFactory()
    {
        $mockDriver = new MappingDriverMock();

        // Class Metadata
        $cm = new DocumentMetadata('Doctrine\\Solr\\Tests\\Mapping\\Document1');
        $cm->addField(
            [
                'name' => 'naame',
                'type' => 'string'
            ]
        );
        $cm->addField(
            [
                'name' => 'id',
                'type' => 'string',
                'uniqueKey' => true
            ]
        );

        $driver = $this->getMock('Doctrine\\Solr\\Metadata\\Driver\\AnnotationDriver', [], [], '', false);
        $driver->expects($this->once())
               ->method('loadMetadataForClass');

        $config = $this->getMock('Doctrine\\Solr\\Configuration', [], [], '', false);
        $config->expects($this->once())
               ->method('getMetadataDriverImpl')
               ->will($this->returnValue($driver));

        $cmf = new ClassMetadataFactoryTestSubject($config);
        $cmf->setMetadataForClass(
            'Doctrine\\Solr\\Tests\\Mapping\\Document1',
            $cm
        );

        $this->assertTrue($cm->hasField('naame'));
        $this->assertEquals(2, count($cm->getFieldNames()));

        $cm1 = $cmf->getMetadataFor('Doctrine\\Solr\\Tests\\Mapping\\Document1');
        $this->assertTrue($cm1->hasField('naame'));
        $this->assertEquals($cm, $cm1);

    }

    public function testClassMetadataFactoryLoadsNestedDocumentsMetadata()
    {
        $mockDriver = new MappingDriverMock();

        // Class Metadata
        $cm = new DocumentMetadata('Doctrine\\Solr\\Tests\\Mapping\\Document1');
        $cm->addField(
            [
                'name' => 'naame',
                'type' => 'string'
            ]
        );
        $cm->addField(
            [
                'name' => 'id',
                'type' => 'string',
                'uniqueKey' => true
            ]
        );

        // Extending Class Metadata
        $cme = new DocumentMetadata('Doctrine\\Solr\\Tests\\Mapping\\Document2');
        $cme->addField(
            [
                'name' => 'anotherProperty',
                'type' => 'string'
            ]
        );

        $driver = $this->getMock('Doctrine\\Solr\\Metadata\\Driver\\AnnotationDriver', [], [], '', false);
        $driver->expects($this->any())
               ->method('loadMetadataForClass');

        $config = $this->getMock('Doctrine\\Solr\\Configuration', [], [], '', false);
        $config->expects($this->once())
               ->method('getMetadataDriverImpl')
               ->will($this->returnValue($driver));

        $cmf = new ClassMetadataFactoryTestSubject($config);
        $cmf->setMetadataForClass(
            'Doctrine\\Solr\\Tests\\Mapping\\Document1',
            $cm
        );
        $cmf->setMetadataForClass(
            'Doctrine\\Solr\\Tests\\Mapping\\Document2',
            $cme
        );

        $cm1 = $cmf->getMetadataFor('Doctrine\\Solr\\Tests\\Mapping\\Document2');
        $this->assertTrue($cm1->hasField('naame'));
        $this->assertTrue($cm1->hasField('id'));
        $this->assertTrue($cm1->hasField('anotherProperty'));

    }
}

/* Test subject class with overriden factory method for mocking purposes */
class ClassMetadataFactoryTestSubject extends ClassMetadataFactory
{
    private $_mockMetadata = array();
    private $_requestedClasses = array();

    /** @override */
    protected function newClassMetadataInstance($className)
    {
        $this->_requestedClasses[] = $className;
        if ( ! isset($this->_mockMetadata[$className])) {
            throw new InvalidArgumentException("No mock metadata found for class $className.");
        }
        return $this->_mockMetadata[$className];
    }

    public function setMetadataForClass($className, $metadata)
    {
        $this->_mockMetadata[$className] = $metadata;
    }
}
