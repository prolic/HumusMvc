<?php

namespace HumusMvc;

use Zend_Controller_Action as ActionController;
use Zend_Controller_Action_Interface as ActionControllerInterface;
use Zend_Controller_Dispatcher_Exception as DispatcherException;
use Zend_Controller_Dispatcher_Standard as StandardDispatcher;
use Zend_Controller_Request_Abstract as Request;
use Zend_Controller_Response_Abstract as Response;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Dispatcher extends StandardDispatcher implements
    ServiceLocatorAwareInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Dispatcher
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be
     * dispatched to a controller.
     *
     * Use this method wisely. By default, the dispatcher will fall back to the
     * default controller (either in the module specified or the global default)
     * if a given controller does not exist. This method returning false does
     * not necessarily indicate the dispatcher will not still dispatch the call.
     *
     * @param Request $action
     * @return boolean
     */
    public function isDispatchable(Request $request)
    {
        $className = $this->getControllerClass($request);
        if (($this->_defaultModule != $this->_curModule)
        || $this->getParam('prefixDefaultModule'))
        {
            $className = $this->formatClassName($this->_curModule, $className);
        }
        if (class_exists($className)) {
            return true;
        }
        return false;
    }

    /**
     * Dispatch to a controller/action
     *
     * - If the container is a Symfony Dependency Injection container, controller and his dependencies
     *   are loaded by the container.
     * - If not the controller is instantiated as it would have been in the standard dispatcher.
     *
     * By default, if a controller is not dispatchable, dispatch() will throw
     * an exception. If you wish to use the default controller instead, set the
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws DispatcherException
     */
    public function dispatch(Request $request, Response $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            $controller = $request->getControllerName();
            if (!$this->getParam('useDefaultControllerAlways') && !empty($controller)) {
                throw new DispatcherException('Invalid controller specified (' . $request->getControllerName() . ')');
            }
            $className = $this->getDefaultControllerClass($request);
        } else {
            $className = $this->getControllerClass($request);
            if (!$className) {
                $className = $this->getDefaultControllerClass($request);
            }
            if (($this->_defaultModule != $this->_curModule)
                || $this->getParam('prefixDefaultModule'))
            {
                $className = $this->formatClassName($this->_curModule, $className);
            }
        }

        $sl = $this->getServiceLocator();

        if ($sl->has($className)) {
            $controller = $sl->get($className);
        } else {
            // default controller without dependencies
            $controller = new $className($request, $this->getResponse(), $this->getParams());
            if ($controller instanceof ServiceLocatorAwareInterface) {
                $controller->setServiceLocator($sl);
            }
        }

        if (!($controller instanceof ActionControllerInterface) &&
            !($controller instanceof ActionController)) {
            // // require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new DispatcherException(
                'Controller "' . $className . '" is not an instance of Zend_Controller_Action_Interface'
            );
        }

        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);

        // by default, buffer output
        $disableOb = $this->getParam('disableOutputBuffering');
        $obLevel   = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }

        try {
            $controller->dispatch($action);
        } catch (\Exception $e) {
            // Clean output buffer on error
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }
            throw $e;
        }

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }
}