<?php

namespace HumusMvc\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Navigation as Navigation;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class NavigationFactory implements FactoryInterface
{
    /**
     * Create navigation service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Navigation
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $navigationConfig = $config['navigation'];
        $navigation = new Navigation($navigationConfig);
        return $navigation;
    }

}