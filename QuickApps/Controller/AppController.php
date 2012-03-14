<?php
/**
 * Application Controller
 *
 * PHP version 5
 *
 * @package  QuickApps.Controller
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class AppController extends Controller {
/**
 * An array containing all the information needed by themes
 * to render each page request.
 *
 * @var array
 */
    public $Layout = array(
        'feed' => null,
        'blocks' => array(),
        'node' => array(),
        'viewMode' => '',
        'header' => array(),
        'footer' => array(),
        'stylesheets' => array(
            'all' => array(),
            'braille' => array(),
            'embossed' => array(),
            'handheld' => array(),
            'print' => array(),
            'projection' => array(),
            'screen' => array(),
            'speech' => array(),
            'tty' => array(),
            'tv' => array(),
            'inline' => array(),
            'import' => array()
        ),
        'javascripts' => array(
            'inline' => array(),
            'file' => array('/system/js/jquery.js', '/system/js/quickapps.js')
        ),
        'meta' => array(),
        'fields' => array()
    );

/**
 * Basic helpers
 *
 * @var array
 */
    public $helpers = array(
        'HookCollection',
        'HooktagsCollection',
        'Layout',
        'Form' => array('className' => 'QaForm'),
        'Html' => array('className' => 'QaHtml'),
        'Session',
        'Cache',
        'Js' => array('Jquery', 'className' => 'QaJs'),
        'Time'
    );

/**
 * Basic models
 *
 * @var array
 */
    public $uses = array(
        'System.Variable',
        'System.Module',
        'Menu.MenuLink',
        'Locale.Language'
    );

/**
 * Basic components
 *
 * @var array
 */
    public $components = array(
        'HookCollection',
        'Security' => array('csrfUseOnce' => false, 'csrfExpires' => '+1 hour'),
        'Session',
        'Cookie',
        'RequestHandler',
        'Acl',
        'Auth',
        'QuickApps',
        'System.JqueryUI'
    );

/**
 * Constructor.
 * Preloads all hook objects.
 *
 * @param CakeRequest $request Request object for this controller
 * @param CakeResponse $response Response object for this controller
 */
    public function __construct($request = null, $response = null) {
        HookCollection::preloadHooks($this);
        parent::__construct($request, $response);
    }

/**
 * Called after the controller action is run, but before the view is rendered. You can use this method
 * to perform logic or set view variables that are required on every request.
 *
 * @return void
 */
    public function beforeRender() {
        if ($this->Layout['feed']) {
            $this->Layout['meta']['link'] = $this->Layout['feed'];
        }

        $this->set('Layout', $this->Layout);

        if ($this->name == 'CakeError') {
            $this->beforeFilter();
            $this->layout = 'error';
        }

        return true;
    }

/**
 * Wrapper method to QuickAppsComponent::title()
 *
 * @see QuickAppsComponent::title()
 */
    public function title($str) {
        return $this->QuickApps->title($str);
    }

/**
 * Wrapper method to QuickAppsComponent::is()
 *
 * @see QuickAppsComponent::is()
 */
    public function is($detect, $p = null) {
        return $this->QuickApps->is($detect, $p);
    }

/**
 * Wrapper method to QuickAppsComponent::flashMsg()
 *
 * @see QuickAppsComponent::flashMsg()
 */
    public function flashMsg($msg, $class = 'success', $id = 'flash') {
        return $this->QuickApps->flashMsg($msg, $class, $id);
    }

/**
 * Wrapper method to QuickAppsComponent::blockPush()
 *
 * @see QuickAppsComponent::blockPush()
 */
    public function blockPush($block = array(), $region = null) {
        return $this->QuickApps->blockPush($block, $region);
    }

/**
 * Wrapper method to HookCollectionComponent::attachModuleHooks()
 *
 * @see HookCollectionComponent::attachModuleHooks()
 */
    public function attachModuleHooks($module) {
        return $this->HookCollection->attachModuleHooks($module);
    }

/**
 * Wrapper method to HookCollectionComponent::detachModuleHooks()
 *
 * @see HookCollectionComponent::detachModuleHooks()
 */
    public function detachModuleHooks($module) {
        return $this->HookCollection->detachModuleHooks($module);
    }

/**
 * Wrapper method to HookCollectionComponent::hook()
 *
 * @see HookCollectionComponent::hook()
 */
    public function hook($hook, &$data = array(), $options = array()) {
        $hook = Inflector::underscore($hook);

        return $this->HookCollection->hook($hook, $data, $options);
    }

/**
 * Wrapper method to HookCollectionComponent::hookDefined()
 *
 * @see HookCollectionComponent::hookDefined()
 */
    public function hookDefined($hook) {
        return $this->HookCollection->hookDefined($hook);
    }

/**
 * Wrapper method to HookCollectionComponent::hookEnable()
 *
 * @see HookCollectionComponent::hookEnable()
 */
    public function hookEnable($hook) {
        return $this->HookCollection->hookEnable($hook);
    }

/**
 * Wrapper method to HookCollectionComponent::hookDisable()
 *
 * @see HookCollectionComponent::hookDisable()
 */
    public function hookDisable($hook) {
        return $this->HookCollection->hookDisable($hook);
    }

/**
 * Wrapper method to Controller::paginate()
 * Adds the `paginate_alter` hook to Controller's paginate method.
 *
 * @see Controller::paginate()
 */
    public function paginate($object = null, $scope = array(), $whitelist = array()) {
        $data = compact('object', 'scope', 'whitelist');

        $this->hook('paginate_alter', $data);
        extract($data);

        return parent::paginate($object, $scope, $whitelist);
    }

/**
 * Wrapper method to QuickAppsComponent::setCrumb()
 *
 * @see QuickAppsComponent::setCrumb()
 */
    public function setCrumb($url = false) {
        if (func_num_args() > 1) {
            foreach (func_get_args() as $arg) {
                $this->QuickApps->setCrumb($arg);
            }
        } else {
            return $this->QuickApps->setCrumb($url);
        }
    }
}