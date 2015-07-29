<?php
namespace Models;
class Users extends \RedBean_SimpleModel {

	// Build a multidimensional array of users in usergroups
	function group($users){
		$usergroups = array();
		foreach($users as $user)
			$usergroups[$user->usergroup][$user->id] = array($user->username, 
															 $user->displayname);
		return $usergroups;
	}

}