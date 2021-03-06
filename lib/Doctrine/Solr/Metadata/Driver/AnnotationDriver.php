<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as DoctrineAnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Solr\Mapping\Annotations as SOLR;
use Doctrine\Solr\Metadata\DocumentMetadata;

/**
 * Designed to load metadata into DocumentMetadata container.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class AnnotationDriver extends DoctrineAnnotationDriver implements Driver
{
    protected $reservedFieldNames = [
        '*',
        'score'
    ];

    protected $entityAnnotationClasses = array(
        "Doctrine\\Solr\\Mapping\\Annotations\\Document" => 1,
    );

    public function loadMetadataForClass($className, ClassMetadata $class)
    {
        if (!($class instanceof DocumentMetadata)) {
            throw new \InvalidArgumentException(
                "\$class param must be a DocumentMetadata object " .
                (new \ReflectionClass($class))->getName() .
                " given"
            );
        }

        /** @var $reflClass ReflectionClass */
        $reflClass = $class->getReflectionClass();

        $documentAnnots = array();
        foreach ($this->reader->getClassAnnotations($reflClass) as $annot) {
            foreach ($this->entityAnnotationClasses as $annotClass => $i) {
                if ($annot instanceof $annotClass) {
                    $documentAnnots[$i] = $annot;
                    continue 2;
                }
            }
        }

        if (!$documentAnnots) {
            throw new \InvalidArgumentException(
                "Class " . $class->getName() . " must have DocumentSolr annotation."
            );
        }

        ksort($documentAnnots);
        $documentAnnot = reset($documentAnnots);

        if (isset($documentAnnot->collection)) {
            $class->setCollection($documentAnnot->collection);
        }

        foreach ($reflClass->getProperties() as $property) {
            if (in_array($property, $this->reservedFieldNames)) {
                throw new \InvalidArgumentException("Reserved SOLR property name: " . $property);
            }

            $mapping = array();

            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof SOLR\Field) {
                    $mapping = array_merge(
                        $mapping,
                        array(
                            'name' => $property->getName(),
                            'type' => $annot->type,
                        )
                    );
                } elseif ($annot instanceof SOLR\UniqueKey) {
                    $mapping = array_merge(
                        $mapping,
                        array(
                            'uniqueKey' => true,
                        )
                    );
                }
                // TODO: add more Annotations
            }

            if ($mapping != array()) {
                $class->addField($mapping);
            }
        }
    }

    /** @override */
    public function isTransient($className)
    {
        // TODO: change if transient annotations would be introduced
        return false;
    }

    /**
     * Registers Annotations namespace for bootstrapping.
     */
    public static function registerAnnotationClasses()
    {
        // directory must match this file directory
        AnnotationRegistry::registerAutoloadNamespace("Doctrine\\Solr\\Mapping\\Annotations", __DIR__.'/../../../../');
    }
}
