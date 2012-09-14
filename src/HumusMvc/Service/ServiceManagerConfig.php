<?php

namespace HumusMvc\Service;

use Zend\Mvc\Service\ServiceManagerConfig as ZendServiceManagerConfig;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage Service
 */
class ServiceManagerConfig extends ZendServiceManagerConfig
{
    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = array(
        'EventManager'  => 'Zend\Mvc\Service\EventManagerFactory',
        'ModuleManager' => 'HumusMvc\Service\ModuleManagerFactory',
    );
}