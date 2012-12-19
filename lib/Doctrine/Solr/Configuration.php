<?php
namespace Doctrine\Solr;

use Doctrine\Solr\Manager\DoctrineSolrManager;
use Solarium\Client;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Solr\Metadata\Driver;
use Doctrine\Common\Annotations\AnnotationReader;

class Configuration extends AbstractConfiguration
{
    /**
     * Sets metadata driver closure.
     * @param callable $driver should return a MappingDriver instance.
     */
    public function setMetadataDriverImpl(callable $driver)
    {
        $this->setAttributeClosure('metadataDriverImpl', $driver);
    }

    /**
     * Returns metadata driver.
     * @return Driver
     */
    public function getMetadataDriverImpl()
    {
        return $this->getAttribute('metadataDriverImpl');
    }

    /**
     * Sets DoctrineSolrManager closure.
     * @param callable $client should return a DoctrineSolrManager instance.
     */
    public function setDoctrineSolrManager(callable $dsm)
    {
        $this->setAttributeClosure('doctrineSolrManager', $dsm);
    }

    /**
     * Returns DoctrineSolrManager.
     * @return DoctrineSolrManager
     */
    public function getDoctrineSolrManager()
    {
        return $this->getAttribute('doctrineSolrManager');
    }

    /**
     * Sets ClassMetadataFactory closure.
     * @param callable $client should return a ClassMetadataFactory instance.
     */
    public function setClassMetadataFactory(callable $cmf)
    {
        $this->setAttributeClosure('classMetadataFactory', $cmf);
    }

    /**
     * Returns ClassMetadataFactory.
     * @return ClassMetadataFactory
     */
    public function getClassMetadataFactory()
    {
        return $this->getAttribute('classMetadataFactory');
    }

    /**
     * Sets solarium client closure.
     * @param callable $client should return a Solarium\Client instance.
     */
    public function setSolariumClientImpl(callable $client)
    {
        $this->setAttributeClosure('solariumClientImpl', $client);
    }

    /**
     * Returns solarium client.
     * @return Solarium\Client
     */
    public function getSolariumClientImpl()
    {
        return $this->getAttribute('solariumClientImpl');
    }

    /**
     * Sets a converter closure.
     * @param callable $converter should return a Doctrine\Solr\Converter\Converter instance.
     */
    public function setConverter(callable $converter)
    {
        $this->setAttributeClosure('converter', $converter);
    }

    /**
     * Returns converter.
     * @return Doctrine\Solr\Converter\Converter
     */
    public function getConverter()
    {
        return $this->getAttribute('converter');
    }

    /**
     * Sets a subscriber closure.
     * @param callable $subscriber should return a Doctrine\Solr\Subscriber\... instance.
     */
    public function setSubscriber(callable $subscriber)
    {
        $this->setAttributeClosure('subscriber', $subscriber);
    }

    /**
     * Returns subscriber.
     * @return Doctrine\Solr\Subscriber\MongoDBSubscriber
     */
    public function getSubscriber()
    {
        return $this->getAttribute('subscriber');
    }

    protected static $defaultConfig = [
        'reader' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
        'mapping_driver' => 'Doctrine\\Solr\\Metadata\\Driver\\AnnotationDriver',
        'solarium_client' => 'Solarium\\Client',
        'solarium_client_config' => [
            'querytype' => [
                Client::QUERY_SELECT => 'Doctrine\\Solr\\QueryType\\Select\\Query',
                Client::QUERY_UPDATE => 'Doctrine\\Solr\\QueryType\\Update\\Query',
            ],
        ],
        'doctrine_solr_manager' => 'Doctrine\\Solr\\Manager\\DoctrineSolrManager',
        'class_metadata_factory' => 'Doctrine\\Solr\\Metadata\\ClassMetadataFactory',
        'converter' => 'Doctrine\\Solr\\Converter\\DocumentConverter',
        'subscriber' => 'Doctrine\\Solr\\Subscriber\\MongoDBSubscriber',
    ];

    /**
     * @param {array} $conf
     * @throws ErrorException
     * @return \Doctrine\Solr\Configuration
     */
    public static function fromConfig(array $conf) {
        $conf['solarium_client_config'] = array_merge(
            self::$defaultConfig['solarium_client_config'],
            (array) $conf['solarium_client_config']
        );
        $config = array_merge(self::$defaultConfig, $conf);

        $configuration = new Configuration();

        $reader = $config['reader'];
        $mapping_driver = $config['mapping_driver'];
        $solarium_client = $config['solarium_client'];
        $solarium_client_config = $config['solarium_client_config'];
        $doctrine_solr_manager = $config['doctrine_solr_manager'];
        $class_metadata_factory = $config['class_metadata_factory'];
        $converter = $config['converter'];
        $subscriber = $config['subscriber'];

        $configuration->setMetadataDriverImpl(function() use ($reader, $mapping_driver) {
            return new $mapping_driver(new $reader());
        });

        $configuration->setSolariumClientImpl(function() use ($solarium_client, $solarium_client_config) {
            return new $solarium_client($solarium_client_config);
        });

        $configuration->setDoctrineSolrManager(function() use ($doctrine_solr_manager, $configuration) {
            return new $doctrine_solr_manager($configuration);
        });

        $configuration->setClassMetadataFactory(function() use ($class_metadata_factory, $configuration) {
            return new $class_metadata_factory($configuration);
        });

        $configuration->setConverter(function() use ($converter, $configuration) {
            return new $converter($configuration);
        });

        $configuration->setSubscriber(function() use ($subscriber, $configuration) {
            return new $subscriber($configuration);
        });

        return $configuration;
    }
}
