<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Cache_Manager as CacheManager;

class CacheManagerFactory implements FactoryInterface
{
    /**
     * Create cache manager service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CacheManager
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['cache_manager'])) {
            throw new Exception\RuntimeException(
                'No cache manager config found.'
            );
        }
        $options = $config['cache_manager'];
        $manager = new CacheManager;
        foreach ($options as $key => $value) {
            if ($manager->hasCacheTemplate($key)) {
                $manager->setTemplateOptions($key, $value);
            } else {
                $manager->setCacheTemplate($key, $value);
            }
        }
        return $manager;
    }
}