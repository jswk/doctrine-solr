<?php
namespace Doctrine\Solr\Tests\Mock;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

class MappingDriverMock implements MappingDriver
{
    public function loadMetadataForClass($className, DoctrineClassMetadata $class)
    {
        return;
    }

    public function getAllClassNames()
    {
        return [];
    }

    public function isTransient($className)
    {
        return false;
    }
}
