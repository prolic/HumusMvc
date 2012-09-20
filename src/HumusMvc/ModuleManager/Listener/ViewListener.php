<?php

namespace HumusMvc\ModuleManager\Listener;

use HumusMvc\Exception;
use HumusMvc\ModuleManager\Feature\ViewProviderInterface;
use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * View listener
 *
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
class ViewListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $configs = array();

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param  EventManagerInterface $events
     * @return ViewListener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'onLoadModule'));
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, array($this, 'onLoadModulesPost'));
        return $this;
    }

    /**
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $key => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$key]);
            }
        }
    }


    /**
     * Retrieve view configuration from module
     *
     * If the module does not implement a specific interface and does not
     * implement a specific method, does nothing. Also, if the return value
     * of that method is not a Config object, or not an array or
     * Traversable that can seed one, does nothing.
     *
     * @param  ModuleEvent $e
     * @return void
     */
    public function onLoadModule(ModuleEvent $e)
    {
        $module = $e->getModule();

        if (!$module instanceof ViewProviderInterface
            && !method_exists($module, 'getViewConfig')
        ) {
            return;
        }

        $viewConfig = $module->getViewConfig();
        if ($viewConfig instanceof Traversable) {
            $viewConfig = ArrayUtils::iteratorToArray($viewConfig);
        }
        if (!is_array($viewConfig)) {
            throw new Exception\InvalidArgumentException(
                sprintf('View config being merged must be an array, '
                    . 'implement the \Traversable interface or be an instance '
                    . 'of Zend\Config\Config, %s given.', gettype($viewConfig))
            );
        }
        // We're keeping track of which modules provided which config
        // The actual merging takes place later. Doing it this way will
        // enable us to provide more powerful debugging tools for
        // showing which modules overrode what.
        $this->configs[$e->getModuleName()] = $viewConfig;
    }

    /**
     * Update the front controller configuration in application config
     *
     * @param \Zend\ModuleManager\ModuleEvent $e
     * @return void
     */
    public function onLoadModulesPost(ModuleEvent $e)
    {
        $serviceManager = $this->serviceManager;
        $appConfig = $serviceManager->get('Config');
        if (!isset($appConfig['view'])) {
            $appConfig['view'] = array();
        }
        foreach ($this->configs as $config) {
            $appConfig['view'] = ArrayUtils::merge($config, $appConfig['view']);
        }
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Config', $appConfig);
        $serviceManager->setAllowOverride(false);
    }

}
