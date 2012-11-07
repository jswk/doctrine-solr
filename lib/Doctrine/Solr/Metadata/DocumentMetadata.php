<?php
namespace Doctrine\Solr\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;

/**
 * Container for class metadata.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class DocumentMetadata implements ClassMetadata, DoctrineClassMetadata
{
    /**
     * These values are derived from schema.xml of Solr collection.
     * @var array
     */
    public static $allowedFieldTypes = ['string' => '*_s', 'text' => '*_t',
            'int' => '*_i', 'long' => '*_l', 'float' => '*_f',
            'double' => '*_d', 'date' => '*_dt', 'boolean' => '*_b',];

    public $collection;

    public $name;

    public $fields = array();

    public $reflClass;

    /**
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Adds a new field to metadata.
     * Cannot change existing fields and must contain 'name' and 'type' keys.
     *
     * @param array $field
     * @throws InvalidArgumentException
     */
    public function addField(array $field)
    {
        if (!isset($field['type']) || !isset($field['name'])) {
            throw new \InvalidArgumentException(
                    "Field must contain both 'name' and 'type' keys");
        }

        $name = $field['name'];
        unset($field['name']);

        if ($this->hasField($name)) {
            throw new \InvalidArgumentException("Can't edit field information");
        }

        if (!array_key_exists((string) $field['type'],
                $this::$allowedFieldTypes)) {
            throw new \InvalidArgumentException(
                    "Field type " . $field['type'] . " isn't allowed");
        }

        $allowedTags = array('type' => 1, 'uniqueKey' => 1);
        $this->fields[$name] = array_intersect_key($field, $allowedTags);
    }

    /**
     * {inherit-doc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns info about the collection.
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Sets the collection info
     * @param string $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * {inherit-doc}
     */
    public function getReflectionClass()
    {
        if (!isset($this->reflClass)) {
            $this->reflClass = new \ReflectionClass($this->name);
        }
        return $this->reflClass;
    }

    /**
     * {inherit-doc}
     */
    public function hasField($fieldName)
    {
        return isset($this->fields[$fieldName]);
    }

    /**
     * {inherit-doc}
     */
    public function getFieldNames()
    {
        return array_keys($this->fields);
    }

    /**
     * {inherit-doc}
     */
    public function getSolrFieldName($fieldName)
    {
        if (!$this->hasField($fieldName)) {
            throw new \InvalidArgumentException(
                    'Cannot get name of non existent field.');
        }

        return str_replace('*', $fieldName,
                $this::$allowedFieldTypes[$this->getTypeOfField($fieldName)]);
    }

    /**
     * {inherit-doc}
     */
    public function getTypeOfField($fieldName)
    {
        return $this->hasField($fieldName) ? $this
                        ->getField($fieldName)['type'] : null;
    }

    /**
     * {inherit-doc}
     */
    public function isUniqueKey($fieldName)
    {
        $field = $this->getField($fieldName);
        return isset($field['uniqueKey']) ? $field['uniqueKey'] : false;
        return $this->hasField($fieldName) ? ((isset(
                        $this->fields[$fieldName]['uniqueKey'])) ? (bool) $this
                                ->fields[$fieldName]['uniqueKey'] : false)
                : null;

    }

    /**
     * Returns information about field.
     * @param string $fieldName
     * @return array
     */
    public function getField($fieldName)
    {
        if ($this->hasField($fieldName)) {
            return $this->fields[$fieldName];
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array(
            'collection',
            'fields',
            'name',
        );
    }

    public function getIdentifier()
    {
        return array();
    }

    public function isIdentifier($fieldName)
    {
        return false;
    }

    public function hasAssociation($fieldName)
    {
        return false;
    }

    public function isSingleValuedAssociation($fieldName)
    {
        return false;
    }

    public function isCollectionValuedAssociation($fieldName)
    {
        return false;
    }

    public function getAssociationNames()
    {
        return array();
    }

    public function getAssociationTargetClass($assocName)
    {
        return '';
    }

    public function isAssociationInverseSide($assocName)
    {
        return '';
    }

    public function getAssociationMappedByTargetField($assocName)
    {
        return '';
    }

    public function getIdentifierValues($object)
    {
        // TODO: Auto-generated method stub
    }

    public function getIdentifierFieldNames()
    {
        // TODO: Auto-generated method stub
    }
}
