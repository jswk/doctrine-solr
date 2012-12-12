<?php
namespace Doctrine\Solr;

interface IConfiguration
{
    /**
     * Loads config from $conf into Configuration.
     *
     * @param $conf
     * @return IConfiguration
     */
    static function fromConfig(array $conf);
}
