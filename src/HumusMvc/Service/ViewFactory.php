<?php

namespace HumusMvc\Service;

use HumusMvc\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ViewFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // handle configuration
        $config = $serviceLocator->get('Config');
        $viewConfig = isset($config['view']) ? $config['view'] : array();

        $className = isset($viewConfig['classname']) ? $viewConfig['classname'] : 'HumusMvc\View';
        $view = new $className($viewConfig);
        $view->setPluginLoader($serviceLocator->get('ViewHelperManager'), 'helper');
        die('dd');

        if (isset($viewConfig['doctype'])) {
            $view->doctype()->setDoctype(strtoupper($viewConfig['doctype']));
            if (isset($viewConfig['charset']) && $view->doctype()->isHtml5()) {
                $view->headMeta()->setCharset($viewConfig['charset']);
            }
        }
        die('dd');
        if (isset($viewConfig['contentType'])) {
            $view->headMeta()->appendHttpEquiv('Content-Type', $viewConfig['contentType']);
        }

        if (isset($viewConfig['assign']) && is_array($viewConfig['assign'])) {
            $view->assign($viewConfig['assign']);
        }

        if (isset($viewConfig['useViewRenderer']) && true === (bool) $viewConfig['useViewRenderer']) {
            $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            /* @var $viewRenderer \Zend_Controller_Action_Helper_ViewRenderer */
            $viewRenderer->setView($view);
        }
        return $view;
    }
}
