<?php

namespace Controllers;
class Locations extends Base {

	protected $redirect = '@locations';

	function check_empty($f3){
		return !(bool) $this->D->countShared('tools');
	}

}