<?php
namespace Doctrine\Solr\Tests\QueryType\Select;

use Solarium\QueryType\Select\Result\Document;

use Doctrine\Solr\Tests\Mapping\Document1;
use Doctrine\Solr\QueryType\Select\ResponseParser;

use PHPUnit_Framework_TestCase;

class ResponseParserTest extends PHPUnit_Framework_TestCase
{
    private $solrDocument;
    private $document;
    private $responseArray;
    private $rp;

    public function setUp()
    {
        // converted document
        $this->document = new Document1();
        $this->document->id = "asdf";
        $this->document->important = "important value";

        // solr document created from responseArray
        $this->solrDocument = new Document([
            'id_s' => $this->document->id,
            'important_s' => $this->document->important
        ]);

        // what SOLR is expected to return in JSON or something
        $this->responseArray = [
            'id_s' => $this->document->id,
            'important_s' => $this->document->important
        ];

        // mock converter to return the right value
        $converter = $this->getMock('Doctrine\\Solr\\Converter\\DocumentConverter', [], [], '', false);
        $converter->expects($this->once())
                ->method('fromSolrDocument')
                ->with($this->solrDocument, 'Doctrine\\Solr\\Tests\\Mapping\\Document1')
                ->will($this->returnValue($this->document));

        // create callback
        $convert = function ($document) use ($converter) {
            return $converter->fromSolrDocument($document, 'Doctrine\\Solr\\Tests\\Mapping\\Document1');
        };

        // and ResponseParser to be tested
        $this->rp = new ResponseParser($convert);
    }

    public function testResponseParser()
    {
        // create mock response from SOLR server
        $data = [
            'response' => [
                'docs' => [
                    $this->responseArray,
                ],
                'numFound' => 1,
            ],
        ];

        // mock query
        $query = $this->getMock('Doctrine\\Solr\\QueryType\\Select\\Query', [], [], '', false);
        $query->expects($this->once())
              ->method('getOption')
              ->with('documentclass')
              ->will($this->returnValue('Solarium\QueryType\Select\Result\Document'));
        $query->expects($this->once())
              ->method('getComponents')
              ->will($this->returnValue([]));

        // and mock result
        $result = $this->getMock('Solarium\\Core\\Query\\Result\\Result', [], [], '', false);
        $result->expects($this->once())
               ->method('getData')
               ->will($this->returnValue($data));
        $result->expects($this->once())
               ->method('getQuery')
               ->will($this->returnValue($query));

        // parse the result
        $out = $this->rp->parse($result);

        // and check, if it converted the document correctly
        $this->assertEquals($this->document, $out['documents'][0]);
    }
}
