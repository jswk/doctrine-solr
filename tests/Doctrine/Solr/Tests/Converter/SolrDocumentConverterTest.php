<?php
namespace Doctrine\Solr\Tests\Converter;

use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Converter\SolrDocumentConverter;
use PHPUnit_Framework_TestCase;

class SolrDocumentConverterTest extends PHPUnit_Framework_TestCase
{
    public function testConverterBasic()
    {
        $class = new DocumentMetadata('Doctrine\Solr\Tests\Mapping\Document1');
        $class->addField(['name' => 'name', 'type' => 'string']);
        $class->addField(['name' => 'id', 'type' => 'string', 'uniqueKey' => true]);

        $cmf = $this->getMock('Doctrine\Solr\Metadata\ClassMetadataFactory', array(), array(), '', false);
        $cmf->expects($this->once())
            ->method('getMetadataFor')
            ->will($this->returnValue($class));

        $conv = new SolrDocumentConverter($cmf);

        $document = new DocumentMock();
        $document->name = "Name";
        $document->id = "34h7834";
        $document->notMapped = "Unimportant";

        $solrDoc = $conv->toSolrDocument($document);

        $this->assertEquals($document->name, $solrDoc->name_s);
        $this->assertEquals($document->id, $solrDoc->id_s);
        $this->assertNull($solrDoc->notMapped);
    }
}

class DocumentMock
{
    private $fields = array();

    public function __get($name)
    {
        if (!isset($this->fields[$name])) {
            return null;
        }
        return $this->fields[$name];
    }

    public function __set($name, $val)
    {
        $this->fields[$name] = $val;
    }
}