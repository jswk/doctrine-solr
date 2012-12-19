<?php
namespace Doctrine\Solr;
use Doctrine\Common\EventManager;

class Runner
{
    public static function run(Configuration $config, EventManager $em)
    {
        $driver = $config->getMetadataDriverImpl();
        $driver::registerAnnotationClasses();

        $em->addEventSubscriber($config->getSubscriber());
    }
}
