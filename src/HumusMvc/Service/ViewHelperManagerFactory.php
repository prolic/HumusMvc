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
class ViewHelperManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'HumusMvc\View\HelperPluginManager';

    /**
     * Create and return the view helper manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend_View_Helper_Interface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        // override translate view helper, if translator is created by service locator
        if ($serviceLocator->has('Translator')) {
            $plugins->setFactory('translate', function($sm) use ($serviceLocator) {
                $translateViewHelper = new \Zend_View_Helper_Translate($serviceLocator->get('Translator'));
                return $translateViewHelper;
            });
        }

        if ($serviceLocator->has('Navigation')) {
            $plugins->setFactory('navigation', function($sm) use ($serviceLocator) {
                $navigationViewHelper = new \HumusMvc\View\Helper\Navigation();
                $navigationViewHelper->setServiceLocator($serviceLocator);
                return $navigationViewHelper;
            });
        }

        return $plugins;
    }
}
