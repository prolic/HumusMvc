<?php

namespace HumusMvc\View;

use HumusMvc\Exception;
use HumusMvc\View\HelperPluginManager;
use Zend_View;

/**
 * @category   Humus
 * @package    HumusMvc
 */
class View extends Zend_View
{
    /**
     * @var HelperPluginManager
     */
    protected $_helpers;

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

    /**
     * Get helper by name
     *
     * @param $name
     * @return object
     */
    public function getHelper($name)
    {
        return $this->getHelperPluginManager()->get($name);
    }

}
