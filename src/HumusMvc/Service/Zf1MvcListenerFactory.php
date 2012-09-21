<?php

namespace HumusMvc\Service;

use HumusMvc\ModuleManager\Listener\Zf1MvcListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * FrontController listener
 *
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
class Zf1MvcListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listener = new Zf1MvcListener($serviceLocator);
        $listener->addZf1MvcResource(
            'front_controller',
            'FrontControllerProviderInterface',
            'getFrontControllerConfig'
        );
        $listener->addZf1MvcResource(
            'view',
            'ViewProviderInterface',
            'getViewConfig'
        );
        $listener->addZf1MvcResource(
            'layout',
            'LayoutProviderInterface',
            'getLayoutConfig'
        );
        $listener->addZf1MvcResource(
            'cache_manager',
            'CacheManagerProviderInterface',
            'getCacheManagerConfig'
        );
    }
}
