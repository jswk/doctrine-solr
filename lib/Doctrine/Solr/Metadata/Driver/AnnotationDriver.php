<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as BaseAnnotationDriver;
use Doctrine\Solr\Mapping\Annotations as SOLR;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class AnnotationDriver extends BaseAnnotationDriver
{
    protected $entityAnnotationClasses = array(
        "Doctrine\\Solr\\Mapping\\Annotations\\Document" => 1,
    );

    /**
     * Registers Annotations namespace for bootstrapping.
     */
    public static function registerAnnotationClasses()
    {
        AnnotationRegistry::registerAutoloadNamespace("Doctrine\\Solr\\Mapping\\Annotations", __DIR__.'/../Mapping/Annotation/');
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $class)
    {
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

            // non-document class annotations
        }

        if (!$documentAnnots) {
            // TODO: throw specialized exception
        }

        ksort($documentAnnots);
        $documentAnnot = reset($documentAnnots);

        foreach ($reflClass->getProperties() as $property) {
            $mapping = null;

            foreach ($this->reader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof SOLR\Field) {
                    $mapping = array(
                        'fieldName' => $property->getName(),
                        'type' => $annot->type,
                    );
                }
                // TODO: add more Annotations
            }
        }
    }
}