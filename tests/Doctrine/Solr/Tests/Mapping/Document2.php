<?php
namespace Doctrine\Solr\Tests\Mapping;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Solr\Mapping\Annotations as SOLR;

/**
 * @SOLR\Document(collection="test")
 * @ODM\Document(collection="test")
 */
class Document2 extends Document1
{
    /**
     * @ODM\String
     * @SOLR\Field(type="string")
     */
    protected $anotherProperty;
}
