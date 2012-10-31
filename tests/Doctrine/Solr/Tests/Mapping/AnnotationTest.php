<?php

namespace Doctrine\Solr\Tests\Mapping;

use \Doctrine\Common\Annotations\AnnotationReader;
use \PHPUnit_Framework_TestCase;
use \Doctrine\Solr\Tests\Mapping\Document1;

class AnnotationTest extends PHPUnit_Framework_TestCase
{
    /* @var \Doctrine\Common\Annotations\AnnotationReader */
    private $reader;
    private $item;
    private $classAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\Document';
    private $fieldAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\Field';
    private $uniqueAnnotation = 'Doctrine\\Solr\\Mapping\\Annotations\\UniqueKey';

    public function setUp()
    {
        if ($this->reader == null) {
            $this->reader = new AnnotationReader();
        }

        if ($this->item == null) {
            $item = new Document1();
            $item->id = md5("1234567890");
            $item->notImportant = "dull";
            $item->important = "something-important";

            $this->item = $item;
        }
    }

    public function testAnnotationIsReadCorrectly()
    {
        $class = new \ReflectionClass($this->item);

        $annotation = $this->reader->getClassAnnotation($class, $this->classAnnotation);

        $this->assertNotNull($annotation);
        $this->assertInstanceOf($this->classAnnotation, $annotation);
        $this->assertEquals("test", $annotation->collection);

        $count = 0;
        foreach($class->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, $this->fieldAnnotation);
            if ($annotation == null) {
                continue;
            }

            $this->assertInstanceOf($this->fieldAnnotation, $annotation);

            ++$count;
        }

        $this->assertEquals(2, $count);

    }
}