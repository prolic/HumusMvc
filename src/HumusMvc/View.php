<?php

namespace HumusMvc;

use HumusMvc\View\HelperPluginManager;
use Zend_Loader_PluginLoader_Interface as PluginLoader;
use Zend_View_Exception as ViewException;

/**
 * @category   Humus
 * @package    HumusMvc
 */
class View extends \Zend_View
{
    /**
     * Plugin loaders
     * @var array
     */
    private $_loaders = array();

    /**
     * Plugin types
     * @var array
     */
    private $_loaderTypes = array('filter', 'helper');

    /**
     * Set plugin loader for a particular plugin type
     *
     * @param  PluginLoader $loader
     * @param  string $type
     * @return View
     * @throws ViewException
     */
    public function setPluginLoader(PluginLoader $loader, $type)
    {
        $type = strtolower($type);
        if (!in_array($type, $this->_loaderTypes)) {
            // require_once 'Zend/View/Exception.php';
            $e = new ViewException(sprintf('Invalid plugin loader type "%s"', $type));
            $e->setView($this);
            throw $e;
        }
        if ($loader instanceof HelperPluginManager) {
            $loader->setView($this);
        }
        $this->_loaders[$type] = $loader;
        return $this;
    }

    /**
     * Retrieve plugin loader for a specific plugin type
     *
     * @param  string $type
     * @return PluginLoader
     * @throws ViewException
     */
    public function getPluginLoader($type)
    {
        $type = strtolower($type);
        if (!in_array($type, $this->_loaderTypes)) {
            $e = new ViewException(sprintf('Invalid plugin loader type "%s"; cannot retrieve', $type));
            $e->setView($this);
            throw $e;
        }

        if (!array_key_exists($type, $this->_loaders)) {
            $prefix     = 'Zend_View_';
            $pathPrefix = 'Zend/View/';

            $pType = ucfirst($type);
            switch ($type) {
                case 'filter':
                default:
                    $prefix     .= $pType;
                    $pathPrefix .= $pType;
                    $loader = new \Zend_Loader_PluginLoader(array(
                        $prefix => $pathPrefix
                    ));
                    $this->_loaders[$type] = $loader;
                    break;
            }
        }
        return $this->_loaders[$type];
    }

    /**
     * Get a helper by name
     *
     * @param  string $name
     * @return object
     */
    public function getHelper($name)
    {
        return $this->getPluginLoader('helper')->load($name);
    }

    /**
     * Given a base path, add script, helper, and filter paths relative to it
     *
     * Assumes a directory structure of:
     * <code>
     * basePath/
     *     scripts/
     *     filters/
     * </code>
     *
     * @param  string $path
     * @param  string $prefix Prefix to use for helper and filter paths
     * @return View
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $path        = rtrim($path, '/');
        $path        = rtrim($path, '\\');
        $path       .= DIRECTORY_SEPARATOR;
        $classPrefix = rtrim($classPrefix, '_') . '_';
        $this->addScriptPath($path . 'scripts');
        $this->addFilterPath($path . 'filters', $classPrefix . 'Filter');
        return $this;
    }

}