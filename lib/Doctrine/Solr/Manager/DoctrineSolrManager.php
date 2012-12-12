<?php

namespace Doctrine\Solr\Manager;

use Doctrine\Solr\Configuration;

use Doctrine\Solr\Converter\Converter;
use Doctrine\Solr\Metadata\ClassMetadataFactory;

class DoctrineSolrManager
{
    /** @var Configuration */
    protected $config;

    public function __construct(Configuration $config) {
        $this->config = $config;
    }
/*
    public function setClassMetadataFactory(ClassMetadataFactory $cmf)
    {
        $this->cmf = $cmf;
    }

    public function setConverter(Converter $converter)
    {
        $this->converter = $converter;
    }
*/
    public function getClassMetadataFactory()
    {
        if ($this->cmf == null) {
            $this->cmf = $this->config->getClassMetadataFactory();
        }
        return $this->cmf;
    }

    public function getConverter()
    {
        return $this->converter;
    }
}
