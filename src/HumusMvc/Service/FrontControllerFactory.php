<?php

namespace HumusMvc\Service;

use HumusMvc\Controller\Action\Helper\ViewRenderer;
use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;
use Zend_Controller_Front as FrontController;
use Zend_Layout as Layout;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class FrontControllerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $defaultOptions = array(
        'controller_directory' => array(),
        'module_controller_directory_name' => 'controllers',
        'base_url' => null,
        'throw_exceptions' => false,
        'return_response' => false,
        'default_module' => 'default',
        'default_controller_name' => 'index',
        'default_action' => 'index',
        'params' => array(
            'displayExceptions' => false,
            'disableOutputBuffering' => true
        ),
        'plugins' => array()
    );

    /**
     * Create front controller service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return FrontController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $frontController = FrontController::getInstance();

        // handle injections
        $frontController->setDispatcher($serviceLocator->get('Dispatcher'));
        $frontController->setRequest($serviceLocator->get('Request'));
        $frontController->setResponse($serviceLocator->get('Response'));
        $frontController->setRouter($serviceLocator->get('Router'));

        // get config
        $appConfig = $serviceLocator->get('Config');
        if (isset($appConfig['front_controller'])) {
            $config = ArrayUtils::merge($this->defaultOptions, $appConfig['front_controller']);
        } else {
            $config = $this->defaultOptions;
        }

        // handle configuration
        $frontController->setModuleControllerDirectoryName($config['module_controller_directory_name']);
        $frontController->setBaseUrl($config['base_url']);
        $frontController->setControllerDirectory($config['controller_directory']);
        $frontController->throwExceptions($config['throw_exceptions']);
        $frontController->returnResponse($config['return_response']);
        $frontController->setDefaultModule($config['default_module']);
        $frontController->setDefaultAction($config['default_action']);
        $frontController->setDefaultControllerName($config['default_controller_name']);
        $frontController->setParams($config['params']);
        foreach ($config['plugins'] as $plugin) {
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

        // set action helper plugin manager
        $actionHelperManager = $serviceLocator->get('ActionHelperManager');
        \Zend_Controller_Action_HelperBroker::setPluginLoader($actionHelperManager);
        \Zend_Controller_Action_HelperBroker::addHelper($actionHelperManager->get('viewRenderer'));

        // start layout, if needed
        if (isset($appConfig['layout'])) {
            Layout::startMvc($appConfig['layout']);
        }

        return $frontController;
    }
}