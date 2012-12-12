<?php
namespace Doctrine\Solr\Tests;

use Doctrine\Common\Annotations\AnnotationReader;

use Doctrine\Solr\Configuration;

use PHPUnit_Framework_TestCase;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDriverImplementation()
    {
        $config = new Configuration();

        $driver = $this->getMock('Doctrine\\Solr\\Metadata\\Driver\\AnnotationDriver', [], [], '', false);

        $config->setMetadataDriverImpl(function() use ($driver) {
            return $driver;
        });

        $this->assertEquals($driver, $config->getMetadataDriverImpl());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testThrowsBadMethodCallExceptionIfOptionNotSpecifiedEarlier()
    {
        (new Configuration())->getMetadataDriverImpl();
    }

    public function testFromConfigReturnsProperConfiguration()
    {
        $conf = [
            'solarium_client_config' => [
                'host' => 'localhost'
            ],
            'reader' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
        ];

        $config = Configuration::fromConfig($conf);

        $this->assertInstanceOf("Solarium\\Client", $config->getSolariumClientImpl());
        $this->assertInstanceOf("Doctrine\\Solr\\Metadata\\Driver\\AnnotationDriver", $config->getMetadataDriverImpl());

        $this->assertEquals('Doctrine\\Common\\Annotations\\AnnotationReader', get_class($config->getMetadataDriverImpl()->getReader()));
    }
}
