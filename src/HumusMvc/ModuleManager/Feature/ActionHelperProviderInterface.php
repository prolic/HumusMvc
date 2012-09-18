<?php

namespace HumusMvc\ModuleManager\Feature;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage ModuleManager
 */
interface ActionHelperProviderInterface
{
    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getActionHelperConfig();
}
