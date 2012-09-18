<?php

namespace HumusMvc\Service;

use HumusMvc\Exception;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_View_Helper_Navigation as NavigationViewHelper;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class ActionHelperManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'HumusMvc\Controller\Action\HelperPluginManager';

}
