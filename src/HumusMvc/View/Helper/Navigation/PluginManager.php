<?php

namespace HumusMvc\View\Helper\Navigation;

use HumusMvc\Exception;
use HumusMvc\View\HelperPluginManager;
use Zend_View_Helper_Navigation_HelperAbstract as AbstractHelper;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage View
 */
class PluginManager extends HelperPluginManager
{

    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'breadcrumbs' => 'Zend_View_Helper_Navigation_Breadcrumbs',
        'links'       => 'Zend_View_Helper_Navigation_Links',
        'menu'        => 'Zend_View_Helper_Navigation_Menu',
        'sitemap'     => 'Zend_View_Helper_Navigation_Sitemap',
    );

    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of AbstractHelper.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractHelper) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\AbstractHelper',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}