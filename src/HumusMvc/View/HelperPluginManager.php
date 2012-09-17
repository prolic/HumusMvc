<?php

namespace HumusMvc\View;

use HumusMvc\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend_Loader_PluginLoader_Interface as PluginLoaderInterface;
use Zend_View_Interface as ViewInterface;
use Zend_View_Helper_Interface as ViewHelperInterface;

/**
 * Plugin manager implementation for view helpers
 *
 * Enforces that helpers retrieved are instances of
 * Helper\HelperInterface. Additionally, it registers a number of default
 * helpers.
 *
 * @category   Humus
 * @package    HumusMvc
 * @subpackage View
 */
class HelperPluginManager extends AbstractPluginManager implements PluginLoaderInterface
{

    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        // basepath, doctype, and url are set up as factories in the ViewHelperManagerFactory.
        // basepath and url are not very useful without their factories, however the doctype
        // helper works fine as an invokable. The factory for doctype simply checks for the
        // config value from the merged config.
        'action'              => 'Zend_View_Helper_Action',
        'baseUrl'             => 'Zend_View_Helper_BaseUrl',
        'currency'            => 'Zend_View_Helper_Currency',
        'cycle'               => 'Zend_View_Helper_Cycle',
        'declareVars'         => 'Zend_View_Helper_DeclareVars',
        'doctype'             => 'Zend_View_Helper_Doctype',
        'fieldset'            => 'Zend_View_Helper_Fieldset',
        'form'                => 'Zend_View_Helper_Form',
        'formButton'          => 'Zend_View_Helper_FormButton',
        'formCheckbox'        => 'Zend_View_Helper_FormCheckbox',
        'formElement'         => 'Zend_View_Helper_FormElement',
        'formErrors'          => 'Zend_View_Helper_FormErrors',
        'formFile'            => 'Zend_View_Helper_FormFile',
        'formHidden'          => 'Zend_View_Helper_FormHidden',
        'formImage'           => 'Zend_View_Helper_FormImage',
        'formLabel'           => 'Zend_View_Helper_FormLabel',
        'formMultiCheckbox'   => 'Zend_View_Helper_FormMultiCheckbox',
        'formNote'            => 'Zend_View_Helper_FormNote',
        'formPassword'        => 'Zend_View_Helper_FormPassword',
        'formRadio'           => 'Zend_View_Helper_FormRadio',
        'formReset'           => 'Zend_View_Helper_FormReset',
        'formSelect'          => 'Zend_View_Helper_FormSelect',
        'formSubmit'          => 'Zend_View_Helper_FormSubmit',
        'formText'            => 'Zend_View_Helper_FormText',
        'formTextarea'        => 'Zend_View_Helper_FormTextarea',
        'Gravatar'            => 'Zend_View_Helper_Gravatar',
        'headLink'            => 'Zend_View_Helper_HeadLink',
        'headMeta'            => 'Zend_View_Helper_HeadMeta',
        'headScript'          => 'Zend_View_Helper_HeadScript',
        'headStyle'           => 'Zend_View_Helper_HeadStyle',
        'headTitle'           => 'Zend_View_Helper_HeadTitle',
        'htmlElement'         => 'Zend_View_Helper_HtmlElement',
        'htmlFlash'           => 'Zend_View_Helper_HtmlFlash',
        'htmlList'            => 'Zend_View_Helper_HtmlList',
        'htmlObject'          => 'Zend_View_Helper_HtmlObject',
        'htmlPage'            => 'Zend_View_Helper_HtmlPage',
        'htmlQuicktime'       => 'Zend_View_Helper_HtmlQuicktime',
        'inlineScript'        => 'Zend_View_Helper_InlineScript',
        'json'                => 'Zend_View_Helper_Json',
        'layout'              => 'Zend_View_Helper_Layout',
        'navigation'          => 'Zend_View_Helper_Navigation', // overridden by a factory in ViewHelperManagerFactory
        'paginationControl'   => 'Zend_View_Helper_PaginationControl',
        'partial'             => 'Zend_View_Helper_Partial',
        'partialLoop'         => 'Zend_View_Helper_PartialLoop',
        'placeHolder'         => 'Zend_View_Helper_PlaceHolder',
        'renderToPlaceholder' => 'Zend_View_Helper_RenderToPlaceholder',
        'serverUrl'           => 'Zend_View_Helper_ServerUrl',
        'tinySrc'             => 'Zend_View_Helper_TinySrc',
        'translate'           => 'Zend_View_Helper_Translate', // overridden by a factory in ViewHelperManagerFactory
        'url'                 => 'Zend_View_Helper_Url',
        'userAgent'           => 'Zend_View_Helper_UserAgent'
    );

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached renderer and translator, if any, to the currently requested helper.
     *
     * @param  null|ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(array($this, 'injectView'));
    }

    /**
     * Set view
     *
     * @param  ViewInterface $view
     * @return HelperPluginManager
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Retrieve view instance
     *
     * @return null|ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Inject a helper instance with the registered view
     *
     * @param  ViewHelperInterface $helper
     * @return void
     */
    public function injectView(ViewHelperInterface $helper)
    {
        $view = $this->getView();
        if (null === $view) {
            return;
        }
        $helper->setView($view);
    }

    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of ViewHelperInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ViewHelperInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Helper\HelperInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }

    /**
     * Add prefixed paths to the registry of paths
     *
     * @param string $prefix
     * @param string $path
     * @throws Exception\UnsupportedMethodCallException
     */
    public function addPrefixPath($prefix, $path)
    {
        throw new Exception\UnsupportedMethodCallException('method addPrefixPath() is not supported in ' . __CLASS__);
    }

    /**
     * Remove a prefix (or prefixed-path) from the registry
     *
     * @param string $prefix
     * @param string $path OPTIONAL
     * @throws Exception\UnsupportedMethodCallException
     */
    public function removePrefixPath($prefix, $path = null)
    {
        throw new Exception\UnsupportedMethodCallException('method removePrefixPath() is not supported in ' . __CLASS__);
    }

    /**
     * Whether or not a Helper by a specific name
     *
     * @param string $name
     * @throws Exception\UnsupportedMethodCallException
     */
    public function isLoaded($name)
    {
        throw new Exception\UnsupportedMethodCallException('method isLoaded() is not supported in ' . __CLASS__);
    }

    /**
     * Return full class name for a named helper
     *
     * @param string $name
     * @throws Exception\UnsupportedMethodCallException
     */
    public function getClassName($name)
    {
        throw new Exception\UnsupportedMethodCallException('method getClassName() is not supported in ' . __CLASS__);
    }

    /**
     * Load a helper via the name provided
     *
     * @param string $name
     * @return string
     */
    public function load($name)
    {
        return $this->get($name);
    }


}