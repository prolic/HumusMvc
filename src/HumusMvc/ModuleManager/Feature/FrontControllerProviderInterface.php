<?php

namespace HumusMvc\ModuleManager\Feature;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
interface FrontControllerProviderInterface
{
    /**
     * Expected to return Config object or array
     *
     * @return array|\Zend\Config\Config
     */
    public function getFrontControllerConfig();
}
