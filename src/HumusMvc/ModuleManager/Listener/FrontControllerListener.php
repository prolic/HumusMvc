<?php

namespace HumusMvc\ModuleManager\Listener;

use HumusMvc\Exception;
use HumusMvc\ModuleManager\Feature\FrontControllerProviderInterface;
use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Stdlib\ArrayUtils;

/**
 * FrontController listener
 *
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
class FrontControllerListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $listeners = arraY();

    /**
     * @param  EventManagerInterface $events
     * @return FrontControllerListener
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
     * Retrieve front controller configuration from module
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

        if (!$module instanceof FrontControllerProviderInterface
            && !method_exists($module, 'getFrontControllerConfig')
        ) {
            return;
        }

        $frontControllerConfig = $module->getFrontControllerConfig();
        if ($frontControllerConfig instanceof Traversable) {
            $frontControllerConfig = ArrayUtils::iteratorToArray($frontControllerConfig);
        }
        if (!is_array($frontControllerConfig)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Front controller config being merged must be an array, '
                    . 'implement the \Traversable interface or be an instance '
                    . 'of Zend\Config\Config, %s given.', gettype($frontControllerConfig))
            );
        }
        // We're keeping track of which modules provided which config
        // The actual merging takes place later. Doing it this way will
        // enable us to provide more powerful debugging tools for
        // showing which modules overrode what.
        $this->config[$e->getModuleName()] = $frontControllerConfig;
    }

    /**
     * Update the front controller configuration in application config
     *
     * @param \Zend\ModuleManager\ModuleEvent $e
     * @return void
     */
    public function onLoadModulesPost(ModuleEvent $e)
    {
        $appConfig = $e->getConfigListener()->getMergedConfig(false);
        if (!isset($appConfig['front_controller'])) {
            $appConfig['front_controller'] = array();
        }
        foreach ($this->config as $config) {
            $appConfig['front_controller'] = ArrayUtils::merge($appConfig['front_controller'], $config);
        }
        $e->getConfigListener()->setMergedConfig($appConfig);
    }

}
