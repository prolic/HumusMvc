<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Translate;

class TranslatorFactory implements FactoryInterface
{
    /**
     * Create translator adapter
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend_Translate_Adapter
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['humusmvc']['translator'])) {
            throw new Exception\RuntimeException(
                'No translator config found.'
            );
        }
        $options = $config['humusmvc']['translator'];

        if (!isset($options['content']) && !isset($options['data'])) {
            throw new Exception\RuntimeException('No translation source data provided.');
        } else if (array_key_exists('content', $options) && array_key_exists('data', $options)) {
            throw new Exception\RuntimeException(
                'Conflict on translation source data: choose only one key between content and data.'
            );
        }

        if (empty($options['adapter'])) {
            $options['adapter'] = Zend_Translate::AN_ARRAY;
        }

        if (!empty($options['data'])) {
            $options['content'] = $options['data'];
            unset($options['data']);
        }

        if (isset($options['options'])) {
            foreach($options['options'] as $key => $value) {
                $options[$key] = $value;
            }
        }

        if (!empty($options['cache']) && is_string($options['cache'])) {
            $cacheManager = $serviceLocator->get('CacheManager');
            if ($cacheManager->hasCache($options['cache'])) {
                $options['cache'] = $cacheManager->getCache($options['cache']);
            }
        }

        $translate = new Zend_Translate($options);
        return $translate->getAdapter();
    }
}