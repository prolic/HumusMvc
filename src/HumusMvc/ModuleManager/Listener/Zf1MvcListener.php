<?php

namespace HumusMvc\ModuleManager\Listener;

use HumusMvc\Exception;
use Traversable;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * Zf1 Mvc listener
 *
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
class Zf1MvcListener implements ListenerAggregateInterface
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
     * @var array
     */
    protected $resources = array();

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
     * Add zf1 mvc factory
     *
     * @param string $configKey
     * @param string $interface
     * @param string $moduleClassMethod
     * @return Zf1MvcListener
     */
    public function addZf1MvcResource($configKey, $interface, $moduleClassMethod)
    {
        $this->resources[] = array(
            'config_key' => $configKey,
            'interface' => $interface,
            'module_class_method' => $moduleClassMethod
        );
        return $this;
    }

    /**
     * @param  EventManagerInterface $events
     * @return Zf1MvcListener
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
     * Retrieve configuration from module
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

        foreach ($this->resources as $resource) {
            if (!$module instanceof $resource['interface']
                && !method_exists($module, $resource['module_class_method'])
            ) {
                continue;
            }
            $config = $module->{$resource['module_class_method']}();
            if ($config instanceof Traversable) {
                $config = ArrayUtils::iteratorToArray($config);
            }
            if (!is_array($config)) {
                throw new Exception\InvalidArgumentException(
                    sprintf('Config being merged must be an array, '
                        . 'implement the Traversable interface or be an instance '
                        . 'of Zend\Config\Config, %s given.', gettype($config))
                );
            }
            // We're keeping track of which modules provided which config
            // The actual merging takes place later. Doing it this way will
            // enable us to provide more powerful debugging tools for
            // showing which modules overrode what.
            $this->configs[$e->getModuleName()][$resource['config_key']] = $config;
        }
    }

    /**
     * Update the config in application config
     *
     * @param ModuleEvent $e
     * @return void
     */
    public function onLoadModulesPost(ModuleEvent $e)
    {
        $serviceManager = $this->serviceManager;
        $appConfig = $serviceManager->get('Config');
        foreach ($this->resources as $resource) {
            if (!isset($appConfig[$resource['config_key']])) {
                $appConfig[$resource['config_key']] = array();
            }
            foreach ($this->configs as $config) {
                $appConfig[$resource['config_key']] = ArrayUtils::merge($config[$resource['config_key']], $appConfig[$resource['config_key']]);
            }
        }
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Config', $appConfig);
        $serviceManager->setAllowOverride(false);
    }

}
