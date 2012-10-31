<?php

namespace Doctrine\Solr\Metadata;

interface ClassMetadata
{
    /**
     * Get fully-qualified class name of this persistent class.
     *
     * @return string
     */
    function getName();

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return \ReflectionClass
     */
    function getReflectionClass();

    /**
     * Checks whether the class has field.
     *
     * @param string $fieldName
     * @return boolean
     */
    function hasField($fieldName);

    /**
     * Field names of this class.
     *
     * Names aren't converted to Solr format.
     *
     * @return array<string>
     */
    function getFieldNames();

    /**
     * Returns type of the field.
     *
     * @return string
     */
    function getTypeOfField($fieldName);

    /**
     * Returns if field should be unique
     *
     * @param string $fieldName
     * @return boolean
     */
    function isUniqueKey($fieldName);
}