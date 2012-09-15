<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_View_Helper_Interface as ViewHelperInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ViewHelperManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'HumusMvc\View\HelperPluginManager';

    /**
     * Create and return the view helper manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ViewHelperInterface
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        // Configure URL view helper with router
        $plugins->setFactory('url', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Url;
            $helper->setRouter($serviceLocator->get('Router'));

            $match = $serviceLocator->get('application')
                ->getMvcEvent()
                ->getRouteMatch();

            if ($match instanceof RouteMatch) {

                $helper->setRouteMatch($match);
            }

            return $helper;
        });

        $plugins->setFactory('basepath', function($sm) use($serviceLocator) {
            $config = $serviceLocator->get('Config');
            $config = $config['view_manager'];
            $basePathHelper = new ViewHelper\BasePath;
            if (isset($config['base_path'])) {
                $basePath = $config['base_path'];
            } else {
                $basePath = $serviceLocator->get('Request')->getBasePath();
            }
            $basePathHelper->setBasePath($basePath);
            return $basePathHelper;
        });

        /**
         * Configure doctype view helper with doctype from configuration, if available.
         *
         * Other view helpers depend on this to decide which spec to generate their tags
         * based on. This is why it must be set early instead of later in the layout phtml.
         */
        $plugins->setFactory('doctype', function($sm) use($serviceLocator) {
            $config = $serviceLocator->get('Config');
            $config = $config['view_manager'];
            $doctypeHelper = new ViewHelper\Doctype;
            if (isset($config['doctype'])) {
                $doctypeHelper->setDoctype($config['doctype']);
            }
            return $doctypeHelper;
        });

        return $plugins;
    }
}
