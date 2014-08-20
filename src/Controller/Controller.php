<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since	 1.0.0
 * @author	 Christopher Castro <chris@quickapps.es>
 * @link	 http://www.quickappscms.org
 * @license	 http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace QuickApps\Controller;

use Cake\Controller\Controller as CakeCotroller;
use Cake\I18n\I18n;
use QuickApps\Core\HookTrait;
use QuickApps\View\ViewModeTrait;

/**
 * Main controller class for organization of business logic.
 *
 * Provides basic QuickAppsCMS functionality, such as themes handling,
 * user authorization, and more.
 */
class Controller extends CakeCotroller {

	use HookTrait;
	use ViewModeTrait;

/**
 * In use theme name.
 *
 * @var string
 */
	public $theme;

/**
 * Name of the layout that should be used by current theme.
 *
 * @var string
 */
	public $layout;

/**
 * The name of the View class controllers sends output to.
 *
 * @var string
 */
	public $viewClass = 'QuickApps\View\View';

/**
 * An array containing the names of helpers controllers uses.
 *
 * @var array
 */
	public $helpers = [
		'Url' => ['className' => 'QuickApps\View\Helper\UrlHelper'],
		'Html' => ['className' => 'QuickApps\View\Helper\HtmlHelper'],
		'Form' => ['className' => 'QuickApps\View\Helper\FormHelper'],
		'Menu' => ['className' => 'Menu\View\Helper\MenuHelper'],
		'Block.Region',
	];

/**
 * An array containing the names of components controllers uses.
 *
 * @var array
 */
	public $components = [
		'Auth' => [
			'authenticate' => [
				'User.Form' => [
					'username' => 'username',
					'password' => 'password',
					'userModel' => 'User.Users',
					'scope' => ['Users.status' => 1],
					'contain' => ['Roles'],
					'passwordHasher' => 'Default',
				],
				'User.Anonymous',
			],
			'authorize' => ['User.Cached'],
			'loginAction' => ['plugin' => 'User', 'controller' => 'gateway', 'action' => 'login'],
			'logoutRedirect' => '/',
			'unauthorizedRedirect' => ['plugin' => 'User', 'controller' => 'gateway', 'action' => 'unauthorized'],
		],
		'Menu.Breadcrumb',
		'Session',
		'Flash',
	];

/**
 * Constructor.
 *
 * @param \Cake\Network\Request $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param \Cake\Network\Response $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		$this->switchViewMode('default');
		$this->_prepareLanguage();
		$this->_prepareTheme();
	}

/**
 * Shortcut for Controller::set('title_for_layout', ...)
 *
 * @param string $title_for_layout
 * @return void
 */
	protected function title($title_for_layout) {
		$this->set('title_for_layout', $title_for_layout);
	}

/**
 * Shortcut for Controller::set('description_for_layout', ...)
 *
 * @param string $description_for_layout
 * @return void
 */
	protected function description($description_for_layout) {
		$this->set('description_for_layout', $description_for_layout);
	}

/**
 * Prepares the default language to use.
 *
 * This methods apply the following filters looking for language to use:
 *
 * - URL: If current URL is prefixed  with a valid language code and
 *   `url_locale_prefix` option is enabled, URL's language code will be used.
 * - User session: If user is logged in and has selected a valid preferred
 *   language it will be used.
 * - GET parameter: If `locale` GET parameter is present in current request,
 *   and if it a valid language code, it will be used as current language
 *   and also will persisted in "locale session".
 * - Locale session: If `locale` session exists it will be used.
 * - Default site's language will be used otherwise.
 *
 * ---
 * 
 * If `url_locale_prefix` option is enabled, and current request's URL has not
 * language prefix on it, user will be redirected to a locale-prefixed version
 * of the requested URL (using the language code selected as explained above).
 * 
 * For example:
 * 
 *     /article/demo-article.html
 * 
 * Might redirects to:
 * 
 *     /en-us/article/demo-article.html
 *
 * @return void
 */
	protected function _prepareLanguage() {
		$locales = array_keys(quickapps('languages'));
		$localesPattern = '(' . implode('|', array_map('preg_quote', $locales)) . ')';
		$normalizedURL = str_replace('//', '/', "/{$this->request->url}"); // starts with "/""

		if (option('url_locale_prefix') && preg_match("/\/{$localesPattern}\//", $normalizedURL, $matches)) {
			I18n::defaultLocale($matches[1]);
		} elseif ($this->request->is('userLoggedIn') && in_array(user()->locale, $locales)) {
			I18n::defaultLocale(user()->locale);
		} elseif (!empty($this->request->query['locale']) && in_array($this->request->query['locale'], $locales)) {
			$this->request->session()->write('locale', $this->request->query['locale']);
			I18n::defaultLocale($this->request->session()->read('locale'));
		} elseif ($this->request->session()->check('locale') && in_array($this->request->session()->read('locale'), $locales)) {
			I18n::defaultLocale($this->request->session()->read('locale'));
		} elseif (in_array(option('default_language'), $locales)) {
			I18n::defaultLocale(option('default_language'));
		} else {
			I18n::defaultLocale('en-us');
		}

		if (
			option('url_locale_prefix') &&
			!$this->request->is('home') &&
			!preg_match("/\/{$localesPattern}\//", $normalizedURL)
		) {
			$url =  '/' . I18n::defaultLocale() . $normalizedURL;
			$this->redirect($url);
		}
	}

/**
 * Sets the theme to use.
 * 
 * @return void
 */
	protected function _prepareTheme() {
		$this->layout = 'default';
		if (!empty($this->request->params['prefix']) && strtolower($this->request->params['prefix']) === 'admin') {
			$this->theme = option('back_theme');
		} else {
			$this->theme = option('front_theme');
		}

		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}

		if ($this->request->isHome()) {
			$this->layout = 'home';
		}

		if ($this->request->isDashboard()) {
			$this->layout = 'dashboard';
		}
	}

}