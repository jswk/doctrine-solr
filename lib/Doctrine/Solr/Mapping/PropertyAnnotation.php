<?php
namespace Doctrine\Solr\Mapping;
use Doctrine\Solr\Mapping\BaseAnnotation;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 */
abstract class PropertyAnnotation extends BaseAnnotation
{
    /**
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options, array $obligatory = array())
    {
        foreach ((array) $obligatory as $field) {
            if (!isset($options[$field])) {
                throw new \InvalidArgumentException(sprintf("Property '%s' must be defined on annotation '%s'", $field,  get_class($this)));
            }
        }

        foreach ((array) $options as $property => $value) {
            $this->$property = $value;
        }
    }
}
