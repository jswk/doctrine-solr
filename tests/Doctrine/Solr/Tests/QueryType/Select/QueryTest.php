<?php
namespace Doctrine\Solr\Tests\QueryType\Select;
use Doctrine\Solr\Converter\DocumentConverter;

use Doctrine\Solr\Metadata\ClassMetadataFactory;

use Doctrine\Solr\Metadata\DocumentMetadata;

use Solarium\QueryType\Select\Result\Document;

use Doctrine\Solr\Tests\Mapping\Document1;

use Doctrine\Solr\QueryType\Select\Query;

use Doctrine\Solr\Configuration;

use PHPUnit_Framework_TestCase;

class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var ClassMetadataFactory
     */
    private $cmf;

    /**
     * @var DocumentConverter
     */
    private $converter;

    /**
     * @var Query
     */
    private $query;

    private $document;

    private $solrDocument;

    private $queryString;
    /**
     * @var DocumentMetadata
     */
    private $class;

    public function setUp()
    {
        $this->document = new Document1();
        $this->document->id = "asdf";
        $this->document->important = "important value";

        $this->solrDocument = new Document([
            'id_s' => $this->document->id,
            'important_s' => $this->document->important
        ]);

        $this->queryString = '(id_s:"asdf")AND(important_s:"important value"';

        $this->class = new DocumentMetadata('Doctrine\\Solr\\Tests\\Mapping\\Document1');
        $this->class->addField(['name' => 'id', 'type' => 'string', 'uniqueKey' => true]);
        $this->class->addField(['name' => 'important', 'type' => 'string']);

        $this->converter = $this->getMock("Doctrine\\Solr\\Converter\\DocumentConverter", [], [], '', false);
        $this->converter->expects($this->any())
                        ->method('toSolrDocument')
                        ->with($this->document)
                        ->will($this->returnValue($this->solrDocument));
        $this->converter->expects($this->any())
                        ->method('fromSolrDocument')
                        ->with($this->solrDocument)
                        ->will($this->returnValue($this->document));
        $this->converter->expects($this->any())
                        ->method('toQuery')
                        ->will($this->returnValue($this->queryString));

        $this->cmf = $this->getMock('Doctrine\\Solr\\Metadata\\ClassMetadataFactory', array(), array(), '', false);
        $this->cmf->expects($this->once())
                  ->method('getMetadataFor')
                  ->will($this->returnValue($this->class));

        $this->config = $this->getMock("Doctrine\\Solr\\Configuration", [], [], '', false);
        $this->config->expects($this->any())
                     ->method("getConverter")
                     ->will($this->returnValue($this->converter));
        $this->config->expects($this->any())
                     ->method("getClassMetadataFactory")
                     ->will($this->returnValue($this->cmf));

        $this->query = new Query([
            'config' => $this->config,
            'mappedDocument' => 'Doctrine\\Solr\\Tests\\Mapping\\Document1'
        ]);
    }

    public function testInit()
    {
        $this->assertEquals('Doctrine\\Solr\\Tests\\Mapping\\Document1', $this->query->getOption('mappedDocument'));
    }

    public function testAddField()
    {
        $this->query->addField('id');

        $this->assertContains($this->class->getSolrFieldName('id'), $this->query->getFields());
    }

    /**
     * @depends testAddField
     */
    public function testRemoveField()
    {
        $this->query->removeField('id');

        $this->assertNotContains($this->class->getSolrFieldName('id'), $this->query->getFields());
    }

    public function testSetQueryByDocument()
    {
        $this->query->setQueryByDocument($this->document);

        $this->assertEquals($this->queryString, $this->query->getQuery());
    }
}
