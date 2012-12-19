Doctrine-Solr
=============

Introduction
------------
Doctrine-Solr is supposed to provide easy to use, annotation based
system to connect SOLR indexer to existing Doctrine utilizing
database. Currently linker for MongoDB is available.

Installation
------------
The system is based on a single Configuration object aggregating all objects
needed to work.

To get Configuration object you have to call
    $config = Doctrine\Solr\Configuration::fromConfig(<config array>);
All the components are lazy loaded when needed.

To simply run the application run

    /**
     * @var $em EventManager the Doctrine EventManager used in application 
     */
    Doctrine\Solr\Runner::run($config, $em);

it should set everything up just fine.

It registers annotations and the subscriber.

To actually search or update something just call

    $client =  $config->getSolariumClientImpl();
    
to obtain Solarium\Client instance.

### Search
To obtain SelectQuery run

    $query = $client->createSelect();
    
then to i.e. select by using a Doctrine Document

    $query->setQueryByDocument($document);
    $resultset = $client->execute($query);

### Update
Normally update should be fired automatically from Doctrine, but you can

    $query = $client->createUpdate();
    $query->addDocument($document);
    $result = $client->execute($query);

Annotations
-----------
To enable SOLR indexing of Doctrine-mapped class one must add

    use Doctrine\Solr\Mapping\Annotations as SOLR;
    /**
     * @SOLR\Document()
     */
    
before the mapped class and every mapped field should be annotated with

    /**
     * @SOLR\Field(type=xxx)
     */

This ensures every update of such document using Doctrine will take
effect in SOLR as well.