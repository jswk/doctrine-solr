<?php
namespace Doctrine\Solr\Mapping\Annotations;

use \Doctrine\Solr\Mapping\BaseAnnotation;

/**
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 * @Annotation
 * @Target("CLASS")
 */
class Document extends BaseAnnotation
{
    public $collection;
}
