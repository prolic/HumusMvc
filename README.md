HumusMvc
====================

[![Dependency Status](https://www.versioneye.com/package/php:prolic:humus-mvc/badge.png)](https://www.versioneye.com/package/php:prolic:humus-mvc)
[![Total Downloads](https://poser.pugx.org/prolic/Humus-Mvc/downloads.png)](https://packagist.org/packages/prolic/Humus-Mvc)
[![Latest Stable Version](https://poser.pugx.org/prolic/Humus-Mvc/v/stable.png)](https://packagist.org/packages/prolic/Humus-Mvc)
[![Latest Unstable Version](https://poser.pugx.org/prolic/Humus-Mvc/v/unstable.png)](https://packagist.org/packages/prolic/Humus-Mvc)

HumusMvc integrates Zend Framework 2's ModuleManager and ServiceManager in a ZF1 application. There is also a [HumusMvcSkeletonApplication](https://github.com/prolic/HumusMvcSkeletonApplication). No Zend_Application will be used any more.

Dependencies
------------

 -  [ZendFramework 2.x](https://github.com/zendframework/zf2)
 -  [ZendFramework 1.12.x](http://framework.zend.com)
 -  Any application similar to the
    [HumusMvcSkeletonApplication](https://github.com/prolic/HumusMvcSkeletonApplication)

Installation
------------

Usually you would install HumusMvc in the HumusMvcSkeletonApplication. If you want to follow that steps, please take a look at the installation instructions of the HumusMvcSkeletonApplication.

However, you can install HumusMvc in your custom skeleton application:

 1.  Add `"prolic/humus-mvc": "dev-master"` to your `composer.json`
 2.  Run `php composer.phar install`

Features / Goals
----------------

 - add possibility to use view helpers with service locator [COMPLETE]
 - add possibility to use action helpers with service locator [COMPLETE]
 - add possibility to use controller plugins with service locator [COMPLETE]
 - configure Zend_Controller_Front with service locator [COMPLETE]
 - add tests [INCOMPLETE]
 - refactore translation service [COMPLETE]
 - create Zf1MvcListenerAggregate and collect all mvc resources here [INCOMPLETE]
 - locale (Zend_Locale) will be created and stored in registry on every request [COMPLETE]
 - make dispatching event based [COMPLETE]
 - add documentation [INCOMPLETE]

View Helpers
------------

- When a Zend_Translate or Zend_Translate_Adapter object is known in service locator with the key "Translator", the Zend_View_Helper_Translate will get the translator injected. No need for putting Zend_Translate in Zend_Registry.
- When a Zend_Navigation or Zend_Navigation_Container object is known in service locator with the key "Navigation", a custom HumusMvc\View\Helper\Navigation is used. This special view helper will check the service locator for the navigation object, additionally, if additional Zend_Acl is available with key "Acl" and Zend_Acl_Role_Interface is available with key "AclRole", both get injected in navigation view helper, too. Same for translator under key "Translator". If nothing is known in service locator, the default Zend_View_Helper_Navigation will be used.
- can be configured with module manager: module config key "view_helpers", interface for module class "Zend\ModuleManager\Feature\ViewHelperProviderInterface" and method in module class "getViewHelperConfig"


View Configuration
------------------

Sample view configuration in module.config.php

    return array(
        'view' => array(
            'classname' => 'HumusMvc\View',
            'useViewRenderer' => true,
            'useStreamWrapper' => false,
            'doctype' => 'XHTML1'
        )
    );

classname (optional): The view class to use. Must be an instance of HumusMvc\View
useViewRenderer, doctype, contentType, assign, etc. are default config keys for Zend_View

A special plugin loader (HumusMvc\View\HelperPluginManager) will get injected into the view object.

Front Controller Configuration
------------------------------

Sample front controller configuration in module.config.php:

    return array(
        'front_controller' => array(
            'controller_directory' => array(
                'test' => __DIR__ . '/../src/test/controllers' // key = name of module, value = path to controllers in this module
            ),
            'module_controller_directory_name'=> 'controllers',
            'base_url' => '/',
            'params' => array(
                'displayExceptions' => false, // true for development
                'disableOutputBuffering' => true
            ),
            'plugins' => array(
                'actionStack' => 'Zend_Controller_Plugin_ActionStack',
                'putHandler' => array(
                    'class' => 'Zend_Controller_Plugin_PutHandler',
                    'stack_index' => 10
                ),
            ),
            'throw_exceptions' => false,
            'return_response' => false,
            'default_module' => 'default',
            'default_action' => 'index',
            'default_controller_name' => 'index',
        )
    );

controller_directory: Key = "ModuleName", Value = "Path to controllers in that module"
plugins: Key = "PluginName", Value = "PluginClass" or array (class and stack_index) - if a plugin is registred in service locator, the plugin will be loaded from service locator, otherwise it will simply be instantiated with "new".
base_url, params, module_controller_directory_name, etc. are default config keys for the front controller

A special plugin loader (HumusMvc\Controller\Action\HelperPluginManager) will get injected into the action controller object.

Action helpers can be configures by module manager: module config key "action_helpers", interface for module class "HumusMvc\ModuleManager\Feature\ActionHelperProviderInterface" and method in module class "getActionHelperConfig"
