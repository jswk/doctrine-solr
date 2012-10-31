<?php
namespace Doctrine\Solr\Metadata;
class DocumentMetadata implements ClassMetadata
{
    private $collection;

    private $name;

    private $fields = array();

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addField(array $field)
    {
        $name = $field['name'];
        unset($field['name']);
        if ($this->hasField($name)) {
            throw new InvalidArgumentException("Can't edit field information");
        }
        if (!isset($field['type'])) {
            throw new InvalidArgumentException("Field must contain type key");
        }
        $allowedTags = array('type', 'uniqueKey');
        $this->fields[$name] = array_intersect_key($field, $allowedTags);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function getReflectionClass()
    {
        if (!isset($this->reflClass)) {
            $this->reflClass = new \ReflectionClass($this->name);
        }
        return $this->reflClass;
    }

    public function hasField(string $fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    public function getFieldNames()
    {
        return array_keys($this->fields);
    }

    public function getTypeOfField($fieldName)
    {
        return $this->hasField($fieldName) ?
               $this->fields[$fieldName]['type'] : null;
    }

    public function isUniqueKey($fieldName)
    {
        return $this->hasField($fieldName) ?
               ((isset($this->fields[$fieldName]['uniqueKey'])) ?
               $this->fields[$fieldName]['uniqueKey'] : false) : null;

    }

}
