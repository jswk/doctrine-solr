<?php
namespace Doctrine\Solr;

abstract class AbstractConfiguration implements IConfiguration
{
    private $attributes = array();

    private $closures = array();

    protected function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            if (!isset($this->closures[$name])) {
                throw new \BadMethodCallException("Option " . $name . " hasn't been set.");
            }
            $this->attributes[$name] = $this->closures[$name]();
        }
        return $this->attributes[$name];
    }

    protected function setAttribute($name, $val)
    {
        $this->attributes[$name] = $val;
    }

    protected function setAttributeClosure($name, $closure)
    {
        $this->closures[$name] = $closure;
    }
}
