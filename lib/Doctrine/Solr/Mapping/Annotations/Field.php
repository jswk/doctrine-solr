<?php
namespace Doctrine\Solr\Mapping\Annotations;

use \Doctrine\Solr\Mapping\BaseAnnotation;

/**
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Field extends BaseAnnotation
{
    public $type;

    public $indexed;

    public $stored;

    public $sortMissingLast;

    public $sortMissingFirst;

    public $multiValued;

    public $uniqueKey;

    /* TODO: fetch more Solr options */

    /**
     *
     * @param array $options
     * @throws DocumentAnnotationTypeNotSpecifiedException
     */
    public function __construct(array $options)
    {
        if (!isset($options['type'])) {
            throw new \InvalidArgumentException(sprintf('Value must be defined for %s', get_class($this)));
        }

        foreach ($options as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $property));
            }
            $this->$property = $value;
        }
    }
}
