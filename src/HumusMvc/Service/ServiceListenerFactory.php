<?php

namespace HumusMvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as ZendServiceListenerFactory;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage Service
 */
class ServiceListenerFactory extends ZendServiceListenerFactory
{
    /**
     * Default mvc-related service configuration -- can be overridden by modules.
     *
     * @var array
     */
    protected $defaultServiceConfig = array(
        'invokables' => array(
            'Router'     => 'Zend_Controller_Router_Rewrite',
            'Dispatcher' => 'HumusMvc\Dispatcher',
            'Request'    => 'Zend_Controller_Request_Http',
            'Response'   => 'Zend_Controller_Response_Http'
        ),
        'factories' => array(
            'Application'             => 'HumusMvc\Service\ApplicationFactory',
            'Config'                  => 'Zend\Mvc\Service\ConfigFactory',
            'DependencyInjector'      => 'Zend\Mvc\Service\DiFactory',
            'FrontController'         => 'HumusMvc\Service\FrontControllerFactory',
            'View'                    => 'HumusMvc\Service\ViewFactory',
            'ViewHelperManager'       => 'HumusMvc\Service\ViewHelperManagerFactory',
            'ActionHelperManager'     => 'HumusMvc\Service\ActionHelperManagerFactory',
            'Navigation'              => 'HumusMvc\Service\NavigationFactory',
            'CacheManager'            => 'HumusMvc\Service\CacheManagerFactory',
            'Translator'              => 'HumusMvc\Service\TranslatorFactory',
            'MultiDbManager'          => 'HumusMvc\Service\MultiDbManagerFactory'
        ),
        'aliases' => array(
            'Configuration'                          => 'Config',
            'Di'                                     => 'DependencyInjector',
            'Zend\Di\LocatorInterface'               => 'DependencyInjector',
            'Zend_Controller_Front'                  => 'FrontController',
            'HumusMvc\Dispatcher'                    => 'Dispatcher',
            'Zend_Controller_Router_Rewrite'         => 'Router',
            'Zend_Controller_Request_Http'           => 'Request',
            'Zend_Controller_Response_Http'          => 'Response',
            'Zend_Navigation'                        => 'Navigation',
            'HumusMvc\Service\MultiDbManager'        => 'MultiDbManager',
            'MultiDb'                                => 'MultiDbManager'
        )
  );
}
