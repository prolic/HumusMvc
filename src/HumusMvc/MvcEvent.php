<?php

namespace HumusMvc;

use Zend\EventManager\Event;
use Zend_Controller_Router_Interface as Router;
use Zend_Controller_Request_Abstract as Request;
use Zend_Controller_Response_Abstract as Response;

/**
 * @category   Humus
 * @package    HumusMvc
 */
class MvcEvent extends Event
{
    /**#@+
     * Mvc events triggered by eventmanager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_FINISH         = 'finish';
    /**#@-*/

    protected $application;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Set application instance
     *
     * @param  ApplicationInterface $application
     * @return MvcEvent
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->setParam('application', $application);
        $this->application = $application;
        return $this;
    }

    /**
     * Get application instance
     *
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Get router
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Set router
     *
     * @param Router $router
     * @return MvcEvent
     */
    public function setRouter(Router $router)
    {
        $this->setParam('router', $router);
        $this->router = $router;
        return $this;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param Request $request
     * @return MvcEvent
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param Response $response
     * @return MvcEvent
     */
    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;
        return $this;
    }

    /**
     * Get result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set result
     *
     * @param mixed $result
     * @return MvcEvent
     */
    public function setResult($result)
    {
        $this->setParam('__RESULT__', $result);
        $this->result = $result;
        return $this;
    }
}
