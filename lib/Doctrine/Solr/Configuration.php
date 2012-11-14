<?php
namespace Doctrine\Solr;

use \Doctrine\Solr\Metadata\Driver\MappingDriver;

class Configuration
{
    private $metadataDriverImplClosure;
    private $metadataDriverImpl;

    /**
     * Sets metadata driver closure.
     *
     * @param callable $driver must return a MappingDriver instance.
     */
    public function setMetadataDriverImpl(callable $driver)
    {
        $this->metadataDriverImplClosure = $driver;
    }

    /**
     * Returns metadata driver
     * @return MappingDriver
     */
    public function getMetadataDriverImpl()
    {
        if ($this->metadataDriverImpl == null) {
            $this->metadataDriverImpl = $this->metadataDriverImplClosure();
        }
        return $this->metadataDriverImpl;
    }
}
