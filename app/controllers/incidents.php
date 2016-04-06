<?php

namespace Controllers;
class Incidents extends Base {

	protected $model_lookup = 'name';

	function redirect(){
		return '/'.$this->D->name;
	}

	function index($f3){
		show_page($f3, 'incidents.index');
	}
}
