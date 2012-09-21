<?php

namespace HumusMvc\View;

use HumusMvc\Exception;
use HumusMvc\Service\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
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
        'action'              => 'Zend_View_Helper_Action',
        'baseurl'             => 'Zend_View_Helper_BaseUrl',
        'currency'            => 'Zend_View_Helper_Currency',
        'cycle'               => 'Zend_View_Helper_Cycle',
        'declarevars'         => 'Zend_View_Helper_DeclareVars',
        'doctype'             => 'Zend_View_Helper_Doctype',
        'fieldset'            => 'Zend_View_Helper_Fieldset',
        'form'                => 'Zend_View_Helper_Form',
        'formbutton'          => 'Zend_View_Helper_FormButton',
        'formcheckbox'        => 'Zend_View_Helper_FormCheckbox',
        'formelement'         => 'Zend_View_Helper_FormElement',
        'formerrors'          => 'Zend_View_Helper_FormErrors',
        'formfile'            => 'Zend_View_Helper_FormFile',
        'formhidden'          => 'Zend_View_Helper_FormHidden',
        'formimage'           => 'Zend_View_Helper_FormImage',
        'formlabel'           => 'Zend_View_Helper_FormLabel',
        'formmulticheckbox'   => 'Zend_View_Helper_FormMultiCheckbox',
        'formnote'            => 'Zend_View_Helper_FormNote',
        'formpassword'        => 'Zend_View_Helper_FormPassword',
        'formradio'           => 'Zend_View_Helper_FormRadio',
        'formreset'           => 'Zend_View_Helper_FormReset',
        'formselect'          => 'Zend_View_Helper_FormSelect',
        'formsubmit'          => 'Zend_View_Helper_FormSubmit',
        'formtext'            => 'Zend_View_Helper_FormText',
        'formtextarea'        => 'Zend_View_Helper_FormTextarea',
        'gravatar'            => 'Zend_View_Helper_Gravatar',
        'headlink'            => 'Zend_View_Helper_HeadLink',
        'headmeta'            => 'Zend_View_Helper_HeadMeta',
        'headscript'          => 'Zend_View_Helper_HeadScript',
        'headstyle'           => 'Zend_View_Helper_HeadStyle',
        'headtitle'           => 'Zend_View_Helper_HeadTitle',
        'htmlelement'         => 'Zend_View_Helper_HtmlElement',
        'htmlflash'           => 'Zend_View_Helper_HtmlFlash',
        'htmllist'            => 'Zend_View_Helper_HtmlList',
        'htmlobject'          => 'Zend_View_Helper_HtmlObject',
        'htmlpage'            => 'Zend_View_Helper_HtmlPage',
        'htmlquicktime'       => 'Zend_View_Helper_HtmlQuicktime',
        'inlinescript'        => 'Zend_View_Helper_InlineScript',
        'json'                => 'Zend_View_Helper_Json',
        'layout'              => 'Zend_View_Helper_Layout',
        'navigation'          => 'Zend_View_Helper_Navigation', // overridden in constructor,
                                                                // if following services are configures in service manager:
                                                                // - Navigation - a Zend_Navigation_Container
                                                                // - Translator (optionally for translation)
                                                                // - Acl (optionally for Acl)
                                                                // - AclRole (optionally for Acl)
        'paginationcontrol'   => 'Zend_View_Helper_PaginationControl',
        'partial'             => 'Zend_View_Helper_Partial',
        'partialloop'         => 'Zend_View_Helper_PartialLoop',
        'placeholder'         => 'Zend_View_Helper_PlaceHolder',
        'rendertoplaceholder' => 'Zend_View_Helper_RenderToPlaceholder',
        'serverurl'           => 'Zend_View_Helper_ServerUrl',
        'tinysrc'             => 'Zend_View_Helper_TinySrc',
        'translate'           => 'Zend_View_Helper_Translate', // overridden by a factory in ViewHelperManagerFactory
                                                               // if translator is created by service locator
        'url'                 => 'Zend_View_Helper_Url',
        'useragent'           => 'Zend_View_Helper_UserAgent'
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
        if ($this->has('Navigation')) {
            $this->setInvokableClass('navigation', 'HumusMvc\View\Helper\Navigation');
        }
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
            'Plugin of type %s is invalid; must implement Zend_View_Helper_Interface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

}
