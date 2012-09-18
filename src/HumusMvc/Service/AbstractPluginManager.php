<?php

namespace HumusMvc\Service;

use Zend\ServiceManager\AbstractPluginManager as ZendAbstractPluginManager;
use Zend_Loader_PluginLoader_Interface as PluginLoaderInterface;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
abstract class AbstractPluginManager extends ZendAbstractPluginManager implements PluginLoaderInterface
{
    /**
     * Add prefixed paths to the registry of paths
     *
     * interface method, overridden and does nothing but returning itself
     *
     * @param string $prefix
     * @param string $path
     * @return AbstractPluginManager
     */
    public function addPrefixPath($prefix, $path)
    {
        return $this;
    }

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * interface method, overridden and does nothing but returning itself
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @return AbstractPluginManager
     */
    public function removePrefixPath($prefix, $path = null)
    {
        return $this;
    }

    /**
     * Whether or not a Helper by a specific name
     *
     * @param string $name
     * @return bool
     */
    public function isLoaded($name)
    {
        return $this->has($name);
    }

    /**
     * Return full class name for a named helper
     *
     * @param string $name
     * @return string|false
     */
    public function getClassName($name)
    {
        if (!$this->has($name)) {
            return false;
        }
        $helper = $this->get($name);
        return get_class($helper);
    }

    /**
     * Load a helper via the name provided
     *
     * @param string $name
     * @return object
     */
    public function load($name)
    {
        return $this->get($name);
    }

}