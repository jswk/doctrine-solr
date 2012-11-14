<?php

namespace Doctrine\Solr\Metadata;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use Doctrine\Solr\Metadata\Driver\AnnotationDriver;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 *
 */
class ClassMetadataFactory extends AbstractClassMetadataFactory
{
    protected $cacheSalt = "\$DOCTRINESOLRCLASSMETADATA";

    /** @var \Doctrine\Solr\Metadata\Driver\AnnotationDriver Solr annotation driver */
    private $driver;

    /** @var \Doctrine\Solr\Configuration The Configuration instance */
    private $config;

    /**
     * {inherit-doc}
     */
    protected function initialize()
    {
        $this->driver = $this->config->getMetadataDriverImpl();
        $this->initialized = true;
    }

    /**
     * {inherit-doc}
     */
    protected function getFqcnFromAlias($namespaceAlias, $simpleClassName)
    {
        // TODO : getFqcnFromAlias
        return __NAMESPACE__ . '\\' . $simpleClassName;
    }

    /**
     * {inherit-doc}
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * {inherit-doc}
     */
    protected function wakeupReflection(DoctrineClassMetadata $class, ReflectionService $reflService)
    {
    }

    /**
     * {inherit-doc}
     */
    protected function initializeReflection(DoctrineClassMetadata $class, ReflectionService $reflService)
    {
    }

    /**
     * {inherit-doc}
     */
    protected function isEntity(DoctrineClassMetadata $class)
    {
        return true;
    }

    /**
     * {inherit-doc}
     */
    protected function doLoadMetadata(
        $class,
        $parent,
        $rootEntityFound,
        array $nonSuperclassParents
    ) {
        /** @var $class DocumentMetadata */

        try {
            $this->driver->loadMetadataForClass($class->getName(), $class);
        } catch (\ReflectionException $e) {
            throw new Exception(
                "Error occurred while reading reflection from " . $class->getName(),
                0,
                $e
            );
        }

    }

    /**
     * Creates new DocumentMetadata instance using param $className
     *
     * @param string $className
     * @return Doctrine\Solr\Metadata\DocumentMetadata
     */
    protected function newClassMetadataInstance($className)
    {
        return new DocumentMetadata($className);

    }
}
