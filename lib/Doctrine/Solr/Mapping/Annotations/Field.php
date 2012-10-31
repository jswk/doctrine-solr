<?php
namespace Doctrine\Solr\Mapping\Annotations;

use \Doctrine\Solr\Mapping\PropertyAnnotation;

/**
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Field extends PropertyAnnotation
{
    public $type;

    /**
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        $obligatory = ['type'];

        parent::__construct($options, $obligatory);
    }
}
