<?php
namespace Doctrine\Solr\Mapping\Annotations;

use Doctrine\Solr\Mapping\PropertyAnnotation;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class UniqueKey extends PropertyAnnotation
{
    public function __construct(array $options)
    {
        parent::__construct($options);
    }
}
