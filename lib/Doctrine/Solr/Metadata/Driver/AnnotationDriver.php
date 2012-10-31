<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Solr\Metadata\ClassMetadata;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Solr\Mapping\Annotations as SOLR;

/**
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class AnnotationDriver implements MappingDriver
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
        }

        if (!$documentAnnots) {
            throw new NoSolrDocumentAnnotationException();
        }

        ksort($documentAnnots);
        $documentAnnot = reset($documentAnnots);

        foreach ($reflClass->getProperties() as $property) {
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
        }
    }
}