<?php
namespace Doctrine\Solr\Tests\Mapping;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Solr\Mapping\Annotations as SOLR;

/**
 * @SOLR\Document(collection="test")
 * @ODM\Document(collection="test")
 */
class Document1
{
    /**
     * @ODM\Id
     * @SOLR\Field(type="string")
     * @SOLR\UniqueKey
     */
    protected $id;

    /**
     * @ODM\String
     */
    protected $notImportant;

    /**
     * @ODM\String
     * @SOLR\Field(type="string")
     */
    protected $important;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
