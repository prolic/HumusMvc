<?php

namespace HumusMvc\ModuleManager\Listener;

use HumusMvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend_Locale as Locale;
use Zend_Registry as Registry;

class LocaleListener
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Locale';

    public function __invoke(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $serviceManager->get('Config');

        if (!isset($config['locale'])) {
            // no layout config found, return
            return;
        }
        // set cache in locale to speed up application
        $cacheManager = $serviceManager->get('CacheManager');
        Locale::setCache($cacheManager->getCache('default'));

        $options = $config['locale'];
        if (!isset($options['default'])) {
            $locale = new Locale();
        } elseif(!isset($options['force']) ||
            (bool) $options['force'] == false)
        {
            // Don't force any locale, just go for auto detection
            Locale::setDefault($options['default']);
            $locale = new Locale();
        } else {
            $locale = new Locale($options['default']);
        }
        $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
            ? $options['registry_key']
            : self::DEFAULT_REGISTRY_KEY;
        Registry::set($key, $locale);
    }
}