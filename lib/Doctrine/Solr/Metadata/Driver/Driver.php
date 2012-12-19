<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

interface Driver extends MappingDriver
{
    public static function registerAnnotationClasses();
}
