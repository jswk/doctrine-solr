<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';

$loader->add('Doctrine\\Solr\\Tests', __DIR__);
//$loader->add('Doctrine\\ODM\\MongoDB\\Tests', __DIR__.'/../vendor/doctrine/mongodb-odm/tests');

// use statements
use \Doctrine\Common\Annotations\AnnotationRegistry;
use \Doctrine\Common\EventManager;
use \Doctrine\Solr\Subscriber\MongoDBSubscriber;
use \Doctrine\Solr\Metadata\Driver\AnnotationDriver;


AnnotationDriver::registerAnnotationClasses();
AnnotationRegistry::registerFile(__DIR__ . '/../vendor/doctrine/mongodb-odm/lib/Doctrine/ODM/MongoDB/Mapping/Annotations/DoctrineAnnotations.php');

$em = new EventManager();

$em->addEventSubscriber(new MongoDBSubscriber);