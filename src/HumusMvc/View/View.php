<?php

namespace HumusMvc\View;

use HumusMvc\Exception;
use HumusMvc\View\HelperPluginManager;
use Zend_Loader_PluginLoader_Interface as PluginLoader;
use Zend_View;
use Zend_View_Interface as ViewInterface;
use Zend_View_Exception as ViewException;

/**
 * @category   Humus
 * @package    HumusMvc
 */
class View implements ViewInterface
{

    /**
     * @var HelperPluginManager
     */
    protected $_helpers;
    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * Constructor
     *
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        $this->_view = new Zend_View($config);
    }

    /**
     * Get helper plugin manager instance
     *
     * @return HelperPluginManager
     */
    public function getHelperPluginManager()
    {
        if (null === $this->_helpers) {
            $this->setHelperPluginManager(new HelperPluginManager());
        }
        return $this->_helpers;
    }

    /**
     * Set helper plugin manager instance
     *
     * @param  string|HelperPluginManager $helpers
     * @return View
     * @throws Exception\InvalidArgumentException
     */
    public function setHelperPluginManager($helpers)
    {
        if (is_string($helpers)) {
            if (!class_exists($helpers)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Invalid helper helpers class provided (%s)',
                    $helpers
                ));
            }
            $helpers = new $helpers();
        }
        if (!$helpers instanceof HelperPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Helper helpers must extend HumusMvc\View\HelperPluginManager; got type "%s" instead',
                (is_object($helpers) ? get_class($helpers) : gettype($helpers))
            ));
        }
        $helpers->setView($this);
        $this->_helpers = $helpers;

        return $this;
    }

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this->_view->getEngine();
    }

    /**
     * Set the path to find the view script used by render()
     *
     * @param string|array The directory (-ies) to set as the path. Note that
     * the concrete view implentation may not necessarily support multiple
     * directories.
     * @return View
     */
    public function setScriptPath($path)
    {
        $this->_view->setScriptPath($path);
        return $this;
    }

    /**
     * Retrieve all view script paths
     *
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_view->getScriptPaths();
    }

    /**
     * Set a base path to all view resources
     *
     * @param  string $path
     * @param  string $classPrefix
     * @return View
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        $this->_view->setBasePath($path, $classPrefix);
        return $this;
    }

    /**
     * Add an additional path to view resources
     *
     * @param  string $path
     * @param  string $classPrefix
     * @return View
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $this->_view->addBasePath($path, $classPrefix);
        return $this;
    }

    /**
     * Prevent E_NOTICE for nonexistent values
     *
     * If {@link strictVars()} is on, raises a notice.
     *
     * @param  string $key
     * @return null
     */
    public function __get($key)
    {
        $this->_view->__get($key);
    }

    /**
     * Assign a variable to the view
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_view->__set($key, $val);
    }

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->_view->__isset($key);
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_view->__unset($key);
    }

    /**
     * Assign variables to the view script via differing strategies.
     *
     * Suggested implementation is to allow setting a specific key to the
     * specified value, OR passing an array of key => value pairs to set en
     * masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or array of key
     * => value pairs)
     * @param mixed $value (Optional) If assigning a named variable, use this
     * as the value.
     * @return View
     */
    public function assign($spec, $value = null)
    {
        $this->_view->assign($spec, $value);
        return $this;
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or
     * property overloading ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_view->clearVars();
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        return $this->_view->render($name);
    }

    /**
     * Accesses a helper object from within a script.
     *
     * If the helper class has a 'view' property, sets it with the current view
     * object.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        $helper = $this->getHelperPluginManager()->get($name);
        // call the helper method
        return call_user_func_array(
            array($helper, $name),
            $args
        );
    }

}
