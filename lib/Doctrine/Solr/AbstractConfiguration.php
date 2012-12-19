<?php
namespace Doctrine\Solr;

/**
 * Skeleton for creating IConfiguration implementations.
 *
 * @author Jakub Sawicki <jakub.sawicki@slkt.pl>
 */
abstract class AbstractConfiguration implements IConfiguration
{
    private $attributes = array();

    private $closures = array();

    /**
     * Protected to block direct creation.
     * fromConfig(...) should be used instead.
     */
    protected function __construct() {
    }

    /**
     * Returns (and if necessary initializes the attribute.
     *
     * @param {string} $name
     * @throws \BadMethodCallException
     * @return attribute requested
     */
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

    /**
     * Sets ordinary attribute.
     * @param {string} $name
     * @param $val
     */
    protected function setAttribute($name, $val)
    {
        $this->attributes[$name] = $val;
    }

    /**
     * Sets attribute that is initialized by closure.
     * @param {string} $name
     * @param $closure
     */
    protected function setAttributeClosure($name, $closure)
    {
        $this->closures[$name] = $closure;
    }
}
