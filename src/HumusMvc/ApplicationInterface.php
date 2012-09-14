<?php

namespace HumusMvc;

use Zend\EventManager\EventsCapableInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Controller_Request_Abstract as Request;
use Zend_Controller_Response_Abstract as Response;

/**
 * @category   Humus
 * @package    HumusMvc
 */
interface ApplicationInterface extends EventsCapableInterface
{
    /**
     * Get the locator object
     *
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * Get the request object
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Get the response object
     *
     * @return Response
     */
    public function getResponse();

    /**
     * Run the application
     *
     * @return mixed
     */
    public function run();
}
