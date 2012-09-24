<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Translate;
use Zend_Translate_Adapter;

class TranslatorFactory implements FactoryInterface
{
    /**
     * @var Zend_Translate_Adapter
     */
    protected $translator;

    /**
     * Create translator adapter
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Zend_Translate_Adapter
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['translator'])) {
            throw new Exception\RuntimeException(
                'No translator config found.'
            );
        }
        $allOptions = $config['translator'];

        foreach ($allOptions as $module => $options) {
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
            if ($this->translator instanceof Zend_Translate_Adapter) {
                $this->translator->addTranslation($options);
            } else {
                $translate = new Zend_Translate($options);
                $this->translator = $translate->getAdapter();
            }
        }
        return $this->translator;
    }

}
