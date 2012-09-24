<?php

namespace HumusMvc\Db\Service;

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Cache_Core;
use Zend_Db;
use Zend_Db_Table;

class MultiDbManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['multidb'])) {
            throw new Exception\RuntimeException(
                'No multi db config found.'
            );
        }
        $options = $config['multidb'];

        if (isset($options['defaultMetadataCache'])) {
            $this->setDefaultMetadataCache($options['defaultMetadataCache'], $serviceLocator);
            unset($options['defaultMetadataCache']);
        }

        $dbs = array();
        $defaultAdapter = null;

        foreach ($options as $id => $params) {
            $adapter = $params['adapter'];
            $default = (int) (
                isset($params['isDefaultTableAdapter']) && $params['isDefaultTableAdapter']
                    || isset($params['default']) && $params['default']
            );
            unset(
            $params['adapter'],
            $params['default'],
            $params['isDefaultTableAdapter']
            );

            $dbs[$id] = Zend_Db::factory($adapter, $params);


            if ($default) {
                Zend_Db_Table::setDefaultAdapter($adapter);
                $defaultAdapter = $adapter;
            }
        }

        $manager = new MultiDbManager($dbs, $defaultAdapter);
        return $manager;
    }

    /**
     * Set the default metadata cache
     *
     * @param string|Zend_Cache_Core $cache
     * @return void
     */
    protected function setDefaultMetadataCache($cache, ServiceLocatorInterface $serviceLocator)
    {
        $metadataCache = null;

        if (is_string($cache)) {
            if ($serviceLocator->has($cache)) {
                $metadataCache = $serviceLocator->get($cache);
            }
        } else if ($cache instanceof Zend_Cache_Core) {
            $metadataCache = $cache;
        }

        if ($metadataCache instanceof Zend_Cache_Core) {
            Zend_Db_Table::setDefaultMetadataCache($metadataCache);
        }

    }
}