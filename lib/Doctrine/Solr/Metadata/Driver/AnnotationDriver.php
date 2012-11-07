<?php
namespace Doctrine\Solr\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Solr\Mapping\Annotations as SOLR;
use Doctrine\Solr\Metadata\DocumentMetadata;
use Doctrine\Solr\Metadata\ClassMetadata;

/**
 * Designed to load metadata into DocumentMetadata container.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
class AnnotationDriver implements MappingDriver
{
    protected $entityAnnotationClasses = array(
        "Doctrine\\Solr\\Mapping\\Annotations\\Document" => 1,
    );

    /**
     * The Reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Registers Annotations namespace for bootstrapping.
     */
    public static function registerAnnotationClasses()
    {
        // directory must match this file directory
        AnnotationRegistry::registerAutoloadNamespace("Doctrine\\Solr\\Mapping\\Annotations", __DIR__.'/../../../../');
    }

    public function loadMetadataForClass($className, ClassMetadata $class)
    {
        if (!($class instanceof DocumentMetadata)) {
            throw new \InvalidArgumentException(
                "\$class param must be a DocumentMetadata object " . (new \ReflectionClass($class))->getName() . " given"
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
            throw new NoSolrDocumentAnnotationException();
        }

        ksort($documentAnnots);
        $documentAnnot = reset($documentAnnots);

        if (isset($documentAnnot->collection)) {
            $class->setCollection($documentAnnot->collection);
        }

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

            if ($mapping != array()) {
                $class->addField($mapping);
            }
        }
    }
}