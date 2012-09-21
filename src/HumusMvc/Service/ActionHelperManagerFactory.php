<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_View_Helper_Navigation as NavigationViewHelper;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class ActionHelperManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'HumusMvc\Controller\Action\HelperPluginManager';

    /**
     * Create and return the view helper manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend_View_Helper_Interface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        $plugins->setFactory('viewRenderer', function($sm) use ($serviceLocator) {
            $renderer = new \HumusMvc\Controller\Action\Helper\ViewRenderer();
            $renderer->setServiceLocator($serviceLocator);
            return $renderer;
        });

        return $plugins;
    }
}
