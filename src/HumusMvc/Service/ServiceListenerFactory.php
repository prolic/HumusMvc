<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace HumusMvc\Service;

use Zend\Mvc\Service\ServiceListenerFactory as ZendServiceListenerFactory;

/**
 * @category   Humus
 * @package    HumusMvc
 * @subpackage Service
 */
class ServiceListenerFactory extends ZendServiceListenerFactory
{
    /**
     * Default mvc-related service configuration -- can be overridden by modules.
     *
     * @var array
     */
    protected $defaultServiceConfig = array(
        'invokables' => array(
            'Router'     => 'Zend_Controller_Router_Rewrite',
            'Dispatcher' => 'HumusMvc\Dispatcher',
            'DispatchListener' => 'HumusMvc\DispatchListener',
            'SendResponseListener' => 'HumusMvc\SendResponseListener',
            'Request'    => 'Zend_Controller_Request_Http',
            'Response'   => 'Zend_Controller_Response_Http'
        ),
        'factories' => array(
            'Application'             => 'HumusMvc\Service\ApplicationFactory',
            'Config'                  => 'Zend\Mvc\Service\ConfigFactory',
            'DependencyInjector'      => 'Zend\Mvc\Service\DiFactory',
            'FrontController'         => 'HumusMvc\Service\FrontControllerFactory',
            'View'                    => 'HumusMvc\Service\ViewFactory',
            'ViewHelperManager'       => 'HumusMvc\Service\ViewHelperManagerFactory',
            'ActionHelperManager'     => 'HumusMvc\Service\ActionHelperManagerFactory',
        ),
        'aliases' => array(
            'Configuration'                          => 'Config',
            'Di'                                     => 'DependencyInjector',
            'Zend\Di\LocatorInterface'               => 'DependencyInjector',
            'Zend_Controller_Front'                  => 'FrontController',
            'HumusMvc\Dispatcher'                    => 'Dispatcher',
            'Zend_Controller_Router_Rewrite'         => 'Router',
            'Zend_Controller_Request_Http'           => 'Request',
            'Zend_Controller_Response_Http'          => 'Response',
        )
  );
}
