<?php

namespace Doctrine\Solr\Tests\Mapping;

use Doctrine\Solr\Mapping\PropertyAnnotation;

use \Doctrine\Common\Annotations\AnnotationReader;
use \PHPUnit_Framework_TestCase;
use \Doctrine\Solr\Tests\Mapping\Document1;

class AnnotationTest extends PHPUnit_Framework_TestCase
{
    private $classAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\Document';
    private $fieldAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\Field';
    private $uniqueAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\UniqueKey';

    public function setUp()
    {

    }

    public function testAnnotationIsReadCorrectly()
    {
        $reader = new AnnotationReader();

        $item = new Document1();
        $item->id = md5("1234567890");
        $item->notImportant = "dull";
        $item->important = "something-important";

        $class = new \ReflectionClass($item);

        $annotation = $reader->getClassAnnotation($class, $this->classAnnotation);

        $this->assertNotNull($annotation);
        $this->assertInstanceOf($this->classAnnotation, $annotation);
        $this->assertEquals("test", $annotation->collection);

        $count = 0;
        foreach ($class->getProperties() as $property) {
            $annotation = $reader->getPropertyAnnotation($property, $this->fieldAnnotation);
            if ($annotation == null) {
                continue;
            }

            $this->assertInstanceOf($this->fieldAnnotation, $annotation);

            ++$count;
        }

        $this->assertEquals(2, $count);

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPropertyAnnotationConstructorThrowsInvalidArgumentException()
    {
        $annotation = new TestAnnotation([], ['name']);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBaseAnnotationSetThrowsBadMethodCallException()
    {
        $annotation = new TestAnnotation([]);
        $annotation->nonExistent = 20;
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBaseAnnotationGetThrowsBadMethodCallException()
    {
        $annotation = new TestAnnotation([]);
        $annotation->nonExistent;
    }

    public function testBaseAnnotationGetAndSet()
    {
        $annotation = new TestAnnotation([]);
        $annotation->name = 'Ollie';
        $this->assertEquals('Ollie', $annotation->name);
    }
}

class TestAnnotation extends PropertyAnnotation
{
    protected $name;
}
