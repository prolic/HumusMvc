<?php

namespace HumusMvc\Controller\Action;

use HumusMvc\Exception;
use HumusMvc\Service\AbstractPluginManager;
use Zend_Controller_Action_Helper_Abstract as AbstractActionHelper;

class HelperPluginManager extends AbstractPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'actionStack'               => 'Zend_Controller_Action_Helper_ActionStack',
        'ajaxContext'               => 'Zend_Controller_Action_Helper_AjaxContext',
        'autoCompleteDojo'          => 'Zend_Controller_Action_Helper_AutoCompleteDojo',
        'autoCompleteScriptaculous' => 'Zend_Controller_Action_Helper_AutoCompleteScriptaculous',
        'cache'                     => 'Zend_Controller_Action_Helper_Cache',
        'contextSwitch'             => 'Zend_Controller_Action_Helper_ContextSwitch',
        'flashMessenger'            => 'Zend_Controller_Action_Helper_FlashMessenger',
        'json'                      => 'Zend_Controller_Action_Helper_Json',
        'redirector'                => 'Zend_Controller_Action_Helper_Redirector',
        'url'                       => 'Zend_Controller_Action_Helper_Url',
        'viewRenderer'              => 'HumusMvc\Controller\Action\Helper\ViewRenderer'
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
}