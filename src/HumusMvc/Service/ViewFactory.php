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

use HumusMvc\Application;
use HumusMvc\View\View as View;
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

        $className = isset($viewConfig['classname']) ? $viewConfig['classname'] : 'HumusMvc\View\View';
        $view = new $className($viewConfig);
        if (!$view instanceof View) {
            throw new InvalidArgumentException('View object must extend HumusMvc\View\View');
        }
        $view->setHelperPluginManager($serviceLocator->get('ViewHelperManager'));

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
