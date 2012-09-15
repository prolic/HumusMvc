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
        'doctype'             => 'Zend_View_Helper_Doctype', // overridden by a factory in ViewHelperManagerFactory
        'basepath'            => 'Zend\View\Helper\BasePath',
        'url'                 => 'Zend\View\Helper\Url',
        'cycle'               => 'Zend\View\Helper\Cycle',
        'declarevars'         => 'Zend\View\Helper\DeclareVars',
        'escapehtml'          => 'Zend\View\Helper\EscapeHtml',
        'escapehtmlattr'      => 'Zend\View\Helper\EscapeHtmlAttr',
        'escapejs'            => 'Zend\View\Helper\EscapeJs',
        'escapecss'           => 'Zend\View\Helper\EscapeCss',
        'escapeurl'           => 'Zend\View\Helper\EscapeUrl',
        'gravatar'            => 'Zend\View\Helper\Gravatar',
        'headlink'            => 'Zend\View\Helper\HeadLink',
        'headmeta'            => 'Zend_View_Helper_HeadMeta',
        'headscript'          => 'Zend\View\Helper\HeadScript',
        'headstyle'           => 'Zend\View\Helper\HeadStyle',
        'headtitle'           => 'Zend\View\Helper\HeadTitle',
        'htmlflash'           => 'Zend\View\Helper\HtmlFlash',
        'htmllist'            => 'Zend\View\Helper\HtmlList',
        'htmlobject'          => 'Zend\View\Helper\HtmlObject',
        'htmlpage'            => 'Zend\View\Helper\HtmlPage',
        'htmlquicktime'       => 'Zend\View\Helper\HtmlQuicktime',
        'inlinescript'        => 'Zend\View\Helper\InlineScript',
        'json'                => 'Zend\View\Helper\Json',
        'layout'              => 'Zend\View\Helper\Layout',
        'paginationcontrol'   => 'Zend\View\Helper\PaginationControl',
        'partialloop'         => 'Zend\View\Helper\PartialLoop',
        'partial'             => 'Zend\View\Helper\Partial',
        'placeholder'         => 'Zend\View\Helper\Placeholder',
        'renderchildmodel'    => 'Zend\View\Helper\RenderChildModel',
        'rendertoplaceholder' => 'Zend\View\Helper\RenderToPlaceholder',
        'serverurl'           => 'Zend\View\Helper\ServerUrl',
        'viewmodel'           => 'Zend\View\Helper\ViewModel',
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