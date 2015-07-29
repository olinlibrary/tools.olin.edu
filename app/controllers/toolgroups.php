<?php

namespace Controllers;
class Toolgroups extends Base {

	function check_empty($f3){
		return !(bool) $this->D->countOwn('tools');
	}

}