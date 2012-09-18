<?php

namespace HumusMvc\Service;

use HumusMvc\Controller\Action\Helper\ViewRenderer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class FrontControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $frontController = \Zend_Controller_Front::getInstance();

        // handle injections
        $dispatcher = $serviceLocator->get('Dispatcher');
        $frontController->setDispatcher($dispatcher);

        $request = $serviceLocator->get('Request');
        $frontController->setRequest($request);

        $response = $serviceLocator->get('Response');
        $frontController->setResponse($response);

        $router = $serviceLocator->get('Router');
        $frontController->setRouter($router);

        // handle configuration
        $config = $serviceLocator->get('Config');
        $frontControllerConfig = $config['service_manager']['front_controller'];

        if (isset ($frontControllerConfig['controller_directory'])) {
            $frontController->setControllerDirectory($frontControllerConfig['controller_directory']);
        }

        if (isset($frontControllerConfig['module_controller_directory_name'])) {
            $frontController->setModuleControllerDirectoryName($frontControllerConfig['module_controller_directory_name']);
        }

        if (isset($frontControllerConfig['base_url'])) {
            $frontController->setBaseUrl($frontControllerConfig['base_url']);
        }

        if (isset($frontControllerConfig['params'])) {
            foreach ($frontControllerConfig['params'] as $key => $param) {
                $frontController->setParam($key, $param);
            }
        }

        if (isset($frontControllerConfig['plugins'])) {
            foreach ($frontControllerConfig['plugins'] as $plugin) {
                if (is_array(($plugin))) {
                    $pluginClass = $plugin['class'];
                    $stackIndex = $plugin['stack_index'];
                } else {
                    $pluginClass = $plugin;
                    $stackIndex = null;
                }
                // plugins can be loaded with service locator
                if ($serviceLocator->has($pluginClass)) {
                    $plugin = $serviceLocator->get($pluginClass);
                } else {
                    $plugin = new $pluginClass();
                }
                $frontController->registerPlugin($plugin, $stackIndex);
            }
        }

        if (isset($frontControllerConfigr['throw_exceptions'])) {
            $frontController->throwExceptions($frontControllerConfig['throw_exceptions']);
        }

        if (isset($frontControllerConfig['return_response'])) {
            $frontController->returnResponse($frontControllerConfig['return_response']);
        }

        if (isset($frontControllerConfig['default_module'])) {
            $frontController->setDefaultModule($frontControllerConfig['default_module']);
        }

        if (isset($frontControllerConfig['default_action'])) {
            $frontController->setDefaultAction($frontControllerConfig['default_action']);
        }

        if (isset($frontControllerConfig['default_controller_name'])) {
            $frontController->setDefaultControllerName($frontControllerConfig['default_controller_name']);
        }

        // set action helper plugin manager
        \Zend_Controller_Action_HelperBroker::setPluginLoader($serviceLocator->get('ActionHelperManager'));

        return $frontController;
    }
}