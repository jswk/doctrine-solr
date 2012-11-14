<?php
namespace Doctrine\Solr\Tests\Metadata;

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

        $cmf = new ClassMetadataFactoryTestSubject();
        $cmf->setMetadataFor(
            'Doctrine\\Solr\\Tests\\Mapping\\Document1',
            $cm
        );

        $this->assertTrue($cm->hasField('naame'));
        $this->assertEquals(2, count($cm->getFieldNames()));

        $cm1 = $cmf->getMetadataFor('Doctrine\\Solr\\Tests\\Mapping\\Document1');
        $this->assertTrue($cm1->hasField('naame'));
        $this->assertEquals($cm, $cm1);

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
