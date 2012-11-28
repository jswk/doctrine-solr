<?php
namespace Doctrine\Solr;

class Configuration extends AbstractConfiguration
{
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
     * @param callable $client should return a Solarium\Client instance.
     */
    public function setSolariumClientImpl(callable $client)
    {
        $this->setAttributeClosure('solariumClientImpl', $client);
    }

    /**
     * Returns solarium client.
     *
     * @return Solarium\Client
     */
    public function getSolariumClientImpl()
    {
        return $this->getAttribute('solariumClientImpl');
    }
}
