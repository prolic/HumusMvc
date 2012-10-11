<?php

namespace HumusMvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Stdlib\ArrayUtils;
use Zend_Controller_Response_Abstract as Response;

class DispatchListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach listeners to an event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
    }

    /**
     * Detach listeners from an event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "dispatch" event
     *
     * @param  MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $front = $sm->get('FrontController');
        $front->returnResponse(true); // Response must be always returned
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new Exception\RuntimeException(
                'No default controller directory registered with front controller'
            );
        }
        $response = $front->dispatch();
        return $this->complete($response, $e);
    }

    /**
     * Complete the dispatch
     *
     * @param  Response $response
     * @param  MvcEvent $event
     * @return mixed
     */
    protected function complete(Response $response, MvcEvent $event)
    {
        $event->setResult($response);
        return $response;
    }
}
