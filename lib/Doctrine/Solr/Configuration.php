<?php
namespace Doctrine\Solr;

use Solarium\Client;

use Doctrine\Common\Annotations\Reader;

use Doctrine\Solr\Metadata\Driver\AnnotationDriver;

use Doctrine\Common\Annotations\AnnotationReader;

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

    /**
     * @param {array|Configuration} $conf
     * @throws ErrorException
     * @return \Doctrine\Solr\Configuration
     */
    public static function fromConfig($conf) {
        if ($conf instanceof Configuration) {
            return $conf;
        }

        $configuration = new Configuration();

        if (isset($conf['reader'])) {
            $reader = $conf['reader'];
            if (! $reader instanceof Reader) {
                throw new \ErrorException(
                    "Option 'reader' must implement Doctrine\\Common\\Annotations\\Reader."
                );
            }
        } else {
            $reader = new AnnotationReader();
        }

        $configuration->setMetadataDriverImpl(function() use ($reader) {
            return new AnnotationDriver($reader);
        });

        if (isset($conf['client'])) {
            $solariumClientConfig = $conf['client'];
            if (!is_array($solariumClientConfig)) {
                throw new \ErrorException("Option 'client' must be an array.");
            }
        } else {
            $solariumClientConfig = null;
        }

        $configuration->setSolariumClientImpl(function() use ($solariumClientConfig) {
            return new Client($solariumClientConfig);
        });

        return $configuration;
    }
}
