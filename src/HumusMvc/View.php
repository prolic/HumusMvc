<?php

namespace HumusMvc;

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
     * @param  \Zend_Loader_PluginLoaderInterface $loader
     * @param  string $type
     * @return View
     */
    public function setPluginLoader(\Zend_Loader_PluginLoader $loader, $type)
    {
        $type = strtolower($type);
        if (!in_array($type, $this->_loaderTypes)) {
            // require_once 'Zend/View/Exception.php';
            $e = new \Zend_View_Exception(sprintf('Invalid plugin loader type "%s"', $type));
            $e->setView($this);
            throw $e;
        }

        $this->_loaders[$type] = $loader;
        return $this;
    }
}