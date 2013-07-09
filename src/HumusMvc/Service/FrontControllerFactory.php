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

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;
use Zend_Controller_Action_HelperBroker as ActionHelperBroker;
use Zend_Controller_Front as FrontController;
use Zend_Layout as Layout;

/**
 * @category Humus
 * @package HumusMvc
 * @subpackage Service
 */
class FrontControllerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $defaultOptions = array(
        'controller_directory' => array(),
        'module_controller_directory_name' => 'controllers',
        'base_url' => null,
        'throw_exceptions' => false,
        'return_response' => true,
        'default_module' => 'default',
        'default_controller_name' => 'index',
        'default_action' => 'index',
        'params' => array(
            'displayExceptions' => false,
            'disableOutputBuffering' => true
        ),
        'plugins' => array()
    );

    /**
     * Create front controller service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return FrontController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $frontController = FrontController::getInstance();

        // handle injections
        $frontController->setDispatcher($serviceLocator->get('Dispatcher'));
        $frontController->setRequest($serviceLocator->get('Request'));
        $frontController->setResponse($serviceLocator->get('Response'));
        $frontController->setRouter($serviceLocator->get('Router'));

        // get config
        $appConfig = $serviceLocator->get('Config');
        if (isset($appConfig['front_controller'])) {
            $config = ArrayUtils::merge($this->defaultOptions, $appConfig['front_controller']);
        } else {
            $config = $this->defaultOptions;
        }

        // handle configuration
        $frontController->setModuleControllerDirectoryName($config['module_controller_directory_name']);
        $frontController->setBaseUrl($config['base_url']);
        $frontController->setControllerDirectory($config['controller_directory']);
        $frontController->throwExceptions($config['throw_exceptions']);
        $frontController->returnResponse($config['return_response']);
        $frontController->setDefaultModule($config['default_module']);
        $frontController->setDefaultAction($config['default_action']);
        $frontController->setDefaultControllerName($config['default_controller_name']);
        $frontController->setParams($config['params']);
        foreach ($config['plugins'] as $plugin) {
            if (is_array(($plugin))) {
                $pluginClass = $plugin['class'];
                $stackIndex = $plugin['stack_index'];
            } else {
                $pluginClass = $plugin;
                $stackIndex = null;
            }
            // plugins can be loaded with service locator
            if ($serviceLocator->has($pluginClass)) {
                $plugin = $serviceLocator->get($pluginClass);
            } else {
                $plugin = new $pluginClass();
            }
            $frontController->registerPlugin($plugin, $stackIndex);
        }

        // set action helper plugin manager
        $actionHelperManager = $serviceLocator->get('ActionHelperManager');
        ActionHelperBroker::setPluginLoader($actionHelperManager);
        ActionHelperBroker::addHelper($actionHelperManager->get('viewRenderer'));

        // start layout, if needed
        if (isset($appConfig['layout'])) {
            Layout::startMvc($appConfig['layout']);
        }

        return $frontController;
    }
}