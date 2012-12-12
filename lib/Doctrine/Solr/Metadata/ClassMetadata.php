<?php

namespace Doctrine\Solr\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as BaseClassMetadata;

interface ClassMetadata extends BaseClassMetadata
{
    /**
     * Solr format field name.
     *
     * @param string $fieldName
     * @return array<string>
     */
    public function getSolrFieldName($fieldName);

    /**
     * @return array with solrNames as keys and standardNames as values
     */
    public function getSolrToStandardFieldNameMapping();

    /**
     * Returns type of the field.
     *
     * @return string
     */
    public function getTypeOfField($fieldName);

    /**
     * Returns if field should be unique
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isUniqueKey($fieldName);
}
