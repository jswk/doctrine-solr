<?php
namespace Doctrine\Solr\Metadata;

/**
 * Container for class metadata.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class DocumentMetadata implements ClassMetadata
{
    /**
     * These values are derived from schema.xml of Solr collection.
     * @var array
     */
    public static $allowedFieldTypes = [
        'string' => '*_s',
        'text' => '*_t',
        'int' => '*_i',
        'long' => '*_l',
        'float' => '*_f',
        'double' => '*_d',
        'date' => '*_dt',
        'boolean' => '*_b',
    ];

    public static $pass = [
        '*',
        'score',
    ];

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
                "Field must contain both 'name' and 'type' keys"
            );
        }

        $name = $field['name'];
        unset($field['name']);

        if ($this->hasField($name)) {
            throw new \InvalidArgumentException("Can't edit field information");
        }

        if (!isset($field['solrFieldName'])) {
            $field['solrFieldName'] = $this->convertToSolrFieldName($name, $field['type']);
        }

        $allowedTags = array('type' => 1, 'uniqueKey' => 1, 'solrFieldName' => 1);
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
     * @param {string} $fieldName
     * @param {string} $type
     * @return {string} corresponding solrFieldName
     */
    protected function convertToSolrFieldName($fieldName, $type)
    {
        if (!array_key_exists(
            (string) $type,
            $this::$allowedFieldTypes
        )) {
            throw new \InvalidArgumentException(
                "Field type " . $type . " isn't allowed"
            );
        }

        return str_replace(
            '*',
            $fieldName,
            $this::$allowedFieldTypes[$type]
        );
    }

    /**
     * {inherit-doc}
     */
    public function getSolrFieldName($fieldName)
    {
        if (in_array($fieldName, self::$pass)) {
            return $fieldName;
        }
        if (!$this->hasField($fieldName)) {
            throw new \InvalidArgumentException(
                'Cannot get name of non existent field ' . $fieldName . "."
            );
        }

        return $this->fields[$fieldName]['solrFieldName'];
    }

    /**
     * {inherit-doc}
     */
    public function getSolrToStandardFieldNameMapping() {
        $out = array();
        foreach ($this->fields as $name => $field) {
            $out[$field['solrFieldName']] = $name;
        }
        return $out;
    }

    /**
     * {inherit-doc}
     */
    public function getTypeOfField($fieldName)
    {
        return $this->hasField($fieldName) ?
               $this->getField($fieldName)['type'] : null;
    }

    /**
     * {inherit-doc}
     */
    public function isUniqueKey($fieldName)
    {
        $field = $this->getField($fieldName);
        return isset($field['uniqueKey']) ? $field['uniqueKey'] : false;

    }

    /**
     * Returns information about field.
     * If field doesn't exist returns empty array.
     * @param string $fieldName
     * @return array
     */
    protected function getField($fieldName)
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
        // TODO: DocumentMetadata identifier values
    }

    public function getIdentifierFieldNames()
    {
        // TODO: DocumentMetadata identifier field names
    }
}
