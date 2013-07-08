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

namespace HumusMvc\Controller\Action;

use HumusMvc\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend_Controller_Action_Helper_Abstract as AbstractActionHelper;
use Zend_Loader_PluginLoader_Interface as PluginLoaderInterface;

class HelperPluginManager extends AbstractPluginManager implements PluginLoaderInterface
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'actionstack'               => 'Zend_Controller_Action_Helper_ActionStack',
        'ajaxcontext'               => 'Zend_Controller_Action_Helper_AjaxContext',
        'autocompletedojo'          => 'Zend_Controller_Action_Helper_AutoCompleteDojo',
        'autocompletescriptaculous' => 'Zend_Controller_Action_Helper_AutoCompleteScriptaculous',
        'cache'                     => 'Zend_Controller_Action_Helper_Cache',
        'contextswitch'             => 'Zend_Controller_Action_Helper_ContextSwitch',
        'flashmessenger'            => 'Zend_Controller_Action_Helper_FlashMessenger',
        'json'                      => 'Zend_Controller_Action_Helper_Json',
        'redirector'                => 'Zend_Controller_Action_Helper_Redirector',
        'url'                       => 'Zend_Controller_Action_Helper_Url',
    );

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractActionHelper) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend_Controller_Action_Helper_Abstract',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

    /**
     * Add prefixed paths to the registry of paths
     *
     * interface method, overridden and does nothing but returning itself
     *
     * @param string $prefix
     * @param string $path
     * @return AbstractPluginManager
     */
    public function addPrefixPath($prefix, $path)
    {
        return $this;
    }

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * interface method, overridden and does nothing but returning itself
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @return AbstractPluginManager
     */
    public function removePrefixPath($prefix, $path = null)
    {
        return $this;
    }

    /**
     * Whether or not a Helper by a specific name
     *
     * @param string $name
     * @return bool
     */
    public function isLoaded($name)
    {
        return $this->has($name);
    }

    /**
     * Return full class name for a named helper
     *
     * @param string $name
     * @return string|false
     */
    public function getClassName($name)
    {
        if (!$this->has($name)) {
            return false;
        }
        $helper = $this->get($name);
        return get_class($helper);
    }

    /**
     * Load a helper via the name provided
     *
     * @param string $name
     * @return object
     */
    public function load($name)
    {
        return $this->get($name);
    }

}