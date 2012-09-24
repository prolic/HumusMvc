<?php

namespace HumusMvc\Service;

use HumusMvc\ModuleManager\Listener\Zf1MvcListener;
use HumusMvc\ModuleManager\Listener\LocaleListener;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage Service
 */
class ModuleManagerFactory implements FactoryInterface
{
    /**
     * Creates and returns the module manager
     *
     * Instantiates the default module listeners, providing them configuration
     * from the "module_listener_options" key of the ApplicationConfig
     * service. Also sets the default config glob path.
     *
     * Module manager is instantiated and provided with an EventManager, to which
     * the default listener aggregate is attached. The ModuleEvent is also created
     * and attached to the module manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ModuleManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('ServiceListener')) {
            $serviceLocator->setFactory('ServiceListener', 'HumusMvc\Service\ServiceListenerFactory');
        }
        if (!$serviceLocator->has('Zf1MvcListener')) {
            $serviceLocator->setFactory('Zf1MvcListener', 'HumusMvc\Service\Zf1MvcListenerFactory');
        }

        $configuration    = $serviceLocator->get('ApplicationConfig');
        $listenerOptions  = new ListenerOptions($configuration['module_listener_options']);
        $defaultListeners = new DefaultListenerAggregate($listenerOptions);
        $serviceListener  = $serviceLocator->get('ServiceListener');

        $serviceListener->addServiceManager(
            $serviceLocator,
            'service_manager',
            'Zend\ModuleManager\Feature\ServiceProviderInterface',
            'getServiceConfig'
        );
        $serviceListener->addServiceManager(
            'ViewHelperManager',
            'view_helpers',
            'Zend\ModuleManager\Feature\ViewHelperProviderInterface',
            'getViewHelperConfig'
        );
        $serviceListener->addServiceManager(
            'ActionHelperManager',
            'action_helpers',
            'HumusMvc\ModuleManager\Feature\ActionHelperProviderInterface',
            'getActionHelperConfig'
        );

        $events = $serviceLocator->get('EventManager');
        $events->attach($defaultListeners);
        $events->attach($serviceListener);
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach('HumusMvc\Application', 'bootstrap', new LocaleListener);

        $moduleEvent = new ModuleEvent;
        $moduleEvent->setParam('ServiceManager', $serviceLocator);

        $moduleManager = new ModuleManager($configuration['modules'], $events);
        $moduleManager->setEvent($moduleEvent);

        return $moduleManager;
    }
}
