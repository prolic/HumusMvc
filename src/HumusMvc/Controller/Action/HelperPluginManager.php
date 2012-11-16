<?php

namespace HumusMvc\Controller\Action;

use HumusMvc\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend_Controller_Action_Helper_Abstract as AbstractActionHelper;
use Zend_Loader_PluginLoader_Interface as PluginLoaderInterface;

class HelperPluginManager extends AbstractPluginManager implements PluginLoaderInterface
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'actionstack'               => 'Zend_Controller_Action_Helper_ActionStack',
        'ajaxcontext'               => 'Zend_Controller_Action_Helper_AjaxContext',
        'autocompletedojo'          => 'Zend_Controller_Action_Helper_AutoCompleteDojo',
        'autocompletescriptaculous' => 'Zend_Controller_Action_Helper_AutoCompleteScriptaculous',
        'cache'                     => 'Zend_Controller_Action_Helper_Cache',
        'contextswitch'             => 'Zend_Controller_Action_Helper_ContextSwitch',
        'flashmessenger'            => 'Zend_Controller_Action_Helper_FlashMessenger',
        'json'                      => 'Zend_Controller_Action_Helper_Json',
        'redirector'                => 'Zend_Controller_Action_Helper_Redirector',
        'url'                       => 'Zend_Controller_Action_Helper_Url',
    );

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractActionHelper) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend_Controller_Action_Helper_Abstract',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

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