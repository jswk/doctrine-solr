<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Solr\Metadata\ClassMetadata;

/**
 * Interface for Solr Mapping Driver.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 * @link Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
 *
 */
interface MappingDriver
{
    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $class);
}
