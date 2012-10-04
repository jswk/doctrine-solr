<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';

$loader->add('Unit\\Doctrine\\Solr', __DIR__);

// use statements
use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerFile(__DIR__ . '/../lib/Doctrine/Search/Mapping/Annotations/DoctrineAnnotations.php');
AnnotationRegistry::registerFile(__DIR__ . '/../vendor/doctrine/mongodb-odm/lib/Doctrine/ODM/MongoDB/Mapping/Annotations/DoctrineAnnotations.php');