<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since	 2.0.0
 * @author	 Christopher Castro <chris@quickapps.es>
 * @link	 http://www.quickappscms.org
 * @license	 http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace Hook;

use Cake\Event\Event;
use Cake\Event\EventListener;

/**
 * Applies some Twitter Bootstrap CSS styles to form/html elements.
 *
 * By default all CSS and HTML code generated automatically by QuickAppsCMS
 * follow Twitter Bootstrap's conventions.
 *
 * Anyway you are able to define your own "Stylizer" by creating a Hook Listener
 * with higher priority and stopping hook propagation. This listener has a priory of 10 by default.
 *
 * If you need disable Twitter Bootstrap's CSS classes to be applied, you can set the special `bootstrap`
 * key as FALSE in your array of options. For example:
 *
 *     echo $this->Form->create($userEntity, ['bootstrap' => false]);
 *         echo $this->Form->input('user_name', ['bootstrap' => false]);
 *     echo $this->Form->end();
 */
class TwitterBootstrapHook implements EventListener {

/**
 * Custom templates for FormHelper.
 * 
 * @var array
 */
	protected $_templates = [
		'button' => '<button{{attrs}}>{{text}}</button>',
		'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
		'checkboxFormGroup' => '{{input}}{{label}}',
		'checkboxWrapper' => '<div class="checkbox">{{input}}{{label}}</div>',
		'errorList' => '<ul>{{content}}</ul>',
		'errorItem' => '<li>{{text}}</li>',
		'file' => '<input type="file" name="{{name}}"{{attrs}}>',
		'fieldset' => '<fieldset>{{content}}</fieldset>',
		'formstart' => '<form{{attrs}}>',
		'formend' => '</form>',
		'formGroup' => '{{label}}{{input}}',
		'hiddenblock' => '<div style="display:none;">{{content}}</div>',
		'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
		'inputsubmit' => '<input type="{{type}}"{{attrs}}>',
		'label' => '<label{{attrs}}>{{text}}</label>',
		'legend' => '<legend>{{text}}</legend>',
		'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
		'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
		'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
		'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
		'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
		'radioWrapper' => '{{input}}{{label}}',
		'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
		'dateWidget' => '<div class="row">
			<div class="col-sm-3">{{year}}</div>
			<div class="col-sm-3">{{month}}</div>
			<div class="col-sm-3">{{day}}</div>
			<div class="col-sm-3">{{hour}}</div>
			<div class="col-sm-3">{{minute}}</div>
			<div class="col-sm-3">{{second}}</div>
			<div class="col-sm-3">{{meridian}}</div>
		</div>',
		'error' => '<div class="help-block">{{content}}</div>',
		'submitContainer' => '{{content}}',
		'inputContainer' => '<div class="form-group {{type}}{{required}}">{{content}}</div>',
		'inputContainerError' => '<div class="form-group has-error has-feedback {{type}}{{required}}">{{content}}<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>{{error}}</div>',
		'groupContainer' => '<div class="input {{type}}{{required}} form-group">{{content}}</div>',
		'groupContainerError' => '<div class="input {{type}}{{required}} has-error">{{content}}{{error}}</div>',
		'radioContainer' => '<div class="radio">{{input}}{{label}}</div>',
		'error' => '<p class="text-danger">{{content}}</p>',
		'errorList' => '<ul class="text-danger">{{content}}</ul>',
	];

/**
 * Implemented hook events list.
 *
 * @return array List of implemented hooks
 */
	public function implementedEvents() {
		return [
			'Alter.FormHelper.create' => 'alterFormCreate',
			'Alter.FormHelper.input' => 'alterFormInput',
			'Alter.FormHelper.textarea' => 'alterFormTextarea',
			'Alter.FormHelper.select' => 'alterFormSelect',
			'Alter.FormHelper.button' => 'alterFormButton',
			'Alter.FormHelper.submit' => 'alterFormSubmit',
		];
	}

/**
 * Adds custom templates on Form::create().
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param mixed $model
 * @param array $options
 * @return array
 */
	public function alterFormCreate(Event $event, &$model, &$options) {
		$bootstrap = isset($options['bootstrap']) ? (bool)$options['bootstrap'] : true;

		if ($bootstrap) {
			$this->_addTemplates($event->subject);
		}

		if (isset($options['bootstrap'])) {
			unset($options['bootstrap']);
		}
	}

/**
 * Appends some CSS classes to generic input (text, textarea, select) elements.
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param string $fieldName
 * @param array $options
 * @return void
 */
	public function alterFormInput(Event $event, &$fieldName, &$options) {
		$this->_addTemplates($event->subject);
		if (
			empty($options['type']) ||
			!in_array($options['type'], ['textarea', 'select', 'button', 'submit', 'checkbox'])
		) {
			$options = $this->_addClass($event->subject, $options, 'form-control');
		}
	}

/**
 * Appends some CSS classes to textarea elements.
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param string $fieldName
 * @param array $options
 * @return void
 */
	public function alterFormTextarea(Event $event, &$fieldName, &$options) {
		$this->_addTemplates($event->subject);
		$options = $this->_addClass($event->subject, $options, 'form-control');
	}

/**
 * Appends some CSS classes to select elements.
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param string $fieldName
 * @param array $options
 * @param array $attributes
 * @return void
 */
	public function alterFormSelect(Event $event, &$fieldName, &$options, &$attributes) {
		$this->_addTemplates($event->subject);
		$attributes = $this->_addClass($event->subject, $attributes, 'form-control');
	}

/**
 * Appends some CSS classes to generic buttons.
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param string $title
 * @param array $options
 * @return void
 */
	public function alterFormButton(Event $event, &$title, &$options) {
		$this->_addTemplates($event->subject);
		$options = $this->_addClass($event->subject, $options, 'btn btn-default');
	}

/**
 * Appends some CSS classes to submit buttons.
 *
 * @param \Cake\Event\Event $event The event that was fired
 * @param string $caption
 * @param array $options
 * @return void
 */
	public function alterFormSubmit(Event $event, &$caption, &$options) {
		$this->_addTemplates($event->subject);
		$options = $this->_addClass($event->subject, $options, 'btn btn-primary');
	}

/**
 * Add custom CSS classes to array of options.
 * 
 * @param \Cake\View\Helper\FormHelper $formHelper Instance of FormHelper
 * @param array $options
 * @return array
 */
	protected function _addClass($formHelper, $options, $class) {
		$bootstrap = isset($options['bootstrap']) ? (bool)$options['bootstrap'] : true;

		if ($bootstrap) {
			$options = $formHelper->addClass($options, $class);
		}

		if (isset($options['bootstrap'])) {
			unset($options['bootstrap']);
		}

		return $options;
	}

/**
 * Add custom set of templates to FormHelper.
 * 
 * @param \Cake\View\Helper\FormHelper $formHelper Instance of FormHelper
 * @return void
 */
	protected function _addTemplates($formHelper) {
		if (empty($formHelper->_bootstrapTemplates)) {
			$formHelper->templates($this->_templates);
			$formHelper->_bootstrapTemplates = true;
		}
	}

}