<?php
namespace Doctrine\Solr\Tests;

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
}
