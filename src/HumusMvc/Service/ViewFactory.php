<?php

namespace HumusMvc\Service;

use HumusMvc\Application;
use HumusMvc\View as View;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Controller_Action_HelperBroker as ControllerActionHelper;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ViewFactory implements FactoryInterface
{

    /**
     * Create view service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return View
     * @throws InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // handle configuration
        $config = $serviceLocator->get('Config');
        $viewConfig = isset($config['view']) ? $config['view'] : array();

        $className = isset($viewConfig['classname']) ? $viewConfig['classname'] : 'HumusMvc\View';
        $view = new $className($viewConfig);
        if (!$view instanceof View) {
            throw new InvalidArgumentException('View object must extend HumusMvc\View');
        }
        $view->setPluginLoader($serviceLocator->get('ViewHelperManager'), 'helper');

        if (isset($viewConfig['doctype'])) {
            $view->doctype()->setDoctype(strtoupper($viewConfig['doctype']));
            if (isset($viewConfig['charset']) && $view->doctype()->isHtml5()) {
                $view->headMeta()->setCharset($viewConfig['charset']);
            }
        }

        if (isset($viewConfig['contentType'])) {
            $view->headMeta()->appendHttpEquiv('Content-Type', $viewConfig['contentType']);
        }

        if (isset($viewConfig['assign']) && is_array($viewConfig['assign'])) {
            $view->assign($viewConfig['assign']);
        }

        if (isset($viewConfig['useViewRenderer']) && true === (bool) $viewConfig['useViewRenderer']) {
            $viewRenderer = ControllerActionHelper::getStaticHelper('viewRenderer');
            $viewRenderer->setView($view);
        }
        return $view;
    }
}
