<?php

namespace Baytek\LaravelContent\Helpers;

use Exception;

class MenuItem
{
	private $action = 'url';
	private $append;
	private $class;
	private $confirm;
	private $form;
	private $location = '';
	private $method = 'get';
	private $model = '';
	private $prepend;
	private $text;
	private $type = 'url';


	/**
	 * Create a new menu item instance
	 * @param string $text       Menu item text
	 * @param array  $properties List of properties set to object
	 */
	public function __construct($text, array $properties = []) {
		$this->text = $text;
		$this->uniqid = uniqid('frm_');

		// If no properties are set, assume this is an on page anchor
		if(empty($properties)) {
			$this->location = '#' . str_slug($text);
		}

		collect($properties)->each(function ($value, $property)
		{
			if(!property_exists($this, $property)) {
				throw new Exception("Setting invalid property '$property'");
			}

			// This could be better using magic setter
			$this->$property = $value;
		});

		if(strtolower($this->method) != 'get') {
			$this->form = sprintf('<form id="%1$s" action="%2$s" method="POST" style="display: none;">%3$s</form>',
			    $this->uniqid,
			    call_user_func($this->type, $this->location, $this->model),
			    csrf_field() . method_field(strtoupper($this->method)) ?: ''
			);
		}
	}

	/**
     * To string magic method that renders all properties into an HTML link
     * @return string HTML markup
     */
	public function __toString() {
		$isConfirmation = false;

		if(strtolower($this->method) == 'delete' || !empty($this->confirm)) {
		    $isConfirmation = true;
		    $this->class .= ' confirm';
		    $confirm = $this->confirm ?: 'Are you sure?';
		}

		return sprintf('<a href="%2$s"%3$s%4$s>%5$s</a>%6$s',
		    $this->class ? null: " class=\"action$this->class\"",
		    starts_with($this->location, '#') ? $this->location : call_user_func($this->type, $this->location, $this->model),
		    (strtolower($this->method) == 'get') ? null : " data-form-id='$this->uniqid'",
		    ($isConfirmation ? " data-confirm=\"{$confirm}\"" : ""),
		    $this->prepend . $this->text . $this->append,
		    $this->form
		);
	}

	public static function anchor($text, array $properties = []) {
		return new static($text, $properties);
	}

}