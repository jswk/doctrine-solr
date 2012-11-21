<?php
namespace Doctrine\Solr;

use \Doctrine\Solr\Metadata\Driver\MappingDriver;

class Configuration
{
    private $attributes = array();

    private $closures = array();

    private function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            if (!isset($this->closures[$name])) {
                throw new \BadMethodCallException("Option " . $name . "hasn't been set.");
            }
            $this->attributes[$name] = $this->closures[$name]();
        }
        return $this->attributes[$name];
    }

    private function setAttribute($name, $val)
    {
        $this->attributes[$name] = $val;
    }

    private function setAttributeClosure($name, $closure)
    {
        $this->closures[$name] = $closure;
    }

    /**
     * Sets metadata driver closure.
     *
     * @param callable $driver should return a MappingDriver instance.
     */
    public function setMetadataDriverImpl(callable $driver)
    {
        $this->setAttributeClosure('metadataDriverImpl', $driver);
    }

    /**
     * Returns metadata driver.
     *
     * @return MappingDriver
     */
    public function getMetadataDriverImpl()
    {
        return $this->getAttribute('metadataDriverImpl');
    }

    /**
     * Sets solarium client closure.
     *
     * @param callable $client should return a Solarium_Client instance.
     */
    public function setSolariumClientImpl(callable $client)
    {
        $this->setAttributeClosure('solariumClientImpl', $client);
    }

    /**
     * Returns solarium client.
     *
     * @return Solarium_Client
     */
    public function getSolariumClientImpl()
    {
        return $this->getAttribute('solariumClientImpl');
    }
}
