<?php

class home
{
	public $example;
	public $slider;
	
	public function __construct($params = array()) {
		$this->example = '';
	}
	
	public function showTemplate() {
		include_once _cfg('pages').'/'.get_class().'/index.tpl';
	}
}