<?php

namespace Controllers;
class Users {

	public $D;
	private $dry;
	protected $LDAPhandle;
	public $username;

	function __construct($f3){
		if($f3->exists('POST.username'))
			$this->username = strtolower($f3->get('POST.username'));
		else if($f3->exists('PARAMS.id'))
			$this->username = strtolower($f3->get('PARAMS.id'));
		else if($f3->exists('SESSION.username'))
			$this->username = strtolower($f3->get('SESSION.username'));
	}

	function info($f3){

		$toolgroups = \R::findAll('toolgroups', 'ORDER BY sort_priority DESC, displayname ASC');
		foreach($toolgroups as $key=>$element) $toolgroups[$key]->with("ORDER BY displayname ASC")->ownTools;
		$f3->set('toolgroups', \R::exportAll($toolgroups));

		$user = \R::findOne('users', 'username=?', array($this->username));
		$f3->set('user', $user);

		$trainings = \R::find('trainings', 'users_id=?', array($user->id));
		$f3->set('trainings', $trainings?reset($trainings)->build_lookup($trainings):false);

		show_page($f3, 'user');
	}

	// Display Login Form
	function login_form($f3){
		show_page($f3, 'login');
	}

	// Log in User
	function login_post($f3){
		$this->authenticate($f3);
		$this->update_user($f3);
        $this->set_session($f3);
        $f3->reroute('@home');
    }

	// Log Out User
    function logout($f3){
		$f3->clear('SESSION');
		$f3->reroute('@home');
	}

	// Authenticate User with LDAP
	function authenticate($f3){
		$this->LDAPhandle = ldap_connect('ldap://'.$f3->get('LDAP_SERVER'));
		ldap_set_option($this->LDAPhandle, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->LDAPhandle, LDAP_OPT_REFERRALS, 0);
        if(!@ldap_bind($this->LDAPhandle, $this->username.'@'.$f3->get('LDAP_DOMAIN'), $f3->get('POST.password')))
            $f3->error(403, 'Invalid Credentials');
	}

	// Load User (Used After Object Creation)
	function load($f3, $username){
		$this->D = \R::findOne('users', 'username=?', array($username));

		if(!$this->D){
			$this->username = $username;
			$this->update_user($f3);
		}

		return $this->D;
	}

	// GUIDs are used as to not cause issues if usernames change
	// (if someone's legal name changes or something)
	// They are hashed because they contain some nasty special characters that mysql doesn't like.
	function guid_hash($ldapResult){
		return @openssl_encrypt($ldapResult[0]['objectguid'][0], 'aes128', 0, false, 0);
	}
	function guid_get(){
		return @openssl_decrypt($this->D->guid, 'aes128', 0, 0, 0);
	}

	// Update User's Information from LDAP Server
	function update_user($f3){

		// Search LDAP Server by Username or by GUID
		$attributes = array('displayname', 'samaccountname', 'title', 'department', 'objectguid');
		if(isset($this->D->guid))
			$filter = '(&(objectguid='.ldap_escape($this->guid_get()).'))';
		else
			$filter = '(&(samaccountname='.ldap_escape($this->username).'))';
	  	$ldapResult = ldap_search($this->LDAPhandle, $f3->get('LDAP_BASEDN'), $filter, $attributes);
	  	$ldapResult = ldap_get_entries($this->LDAPhandle, $ldapResult);

	  	if($ldapResult['count'] == 0){
	  		throw new \Exception('Could not find user: '.$this->username);
	  	}

	  	// Lookup User in Database by GUID
	  	if(!$this->D)
			$this->D = \R::findOne('users', 'guid=?', array($this->guid_hash($ldapResult)));
		if(!$this->D){
			$this->dry = true;
			$this->D = \R::dispense('users');
	  		$this->D->guid = $this->guid_hash($ldapResult);
		}

	  	// Update Username & Display Name
        $this->D->username = $ldapResult[0]['samaccountname'][0];
		$this->D->displayname = $ldapResult[0]['displayname'][0];

	  	// Update Active User Status
	  	if(strpos(strtolower($ldapResult[0]['dn']),'inactive') || 
	  	   strpos(strtolower($ldapResult[0]['dn']),'disabledaccounts') || 
	  	   strpos(strtolower($ldapResult[0]['dn']),'withdrawn') || 
	  	   strpos(strtolower($ldapResult[0]['dn']),'alumni'))
	  		$this->D->active = 0;
	  	else
	  		$this->D->active = 1;

		// Update Group (Class Year)
		if(isset($ldapResult[0]['department']) && strtolower($ldapResult[0]['department'][0]) == 'student'){
			if(strtolower(substr($ldapResult[0]['title'][0], 0,8)) == 'class of')
				$this->D->usergroup = substr($ldapResult[0]['title'][0],9,4); // Normal Students
			else $this->D->usergroup = 8888; // Exchange & Cross-Reg Students
		}else $this->D->usergroup = 9999; // Faculty & Staff

		// Save User Record
		try{
			\R::store($this->D);
		}catch(\Exception $e){
			throw new \Exception('Username already taken');
		}

		// Make First User Administrator
		if($this->dry && $this->D->id == 1){
			$this->D->admin = 1;
			\R::store($this->D);
		}
	}

	function set_session($f3){
		$result = array();

		$result['username'] = $this->D->username;
		$result['admin'] = $this->D->admin;
		$result['displayname'] = $this->D->displayname;
		$result['group'] = $this->D->usergroup;
		$result['id'] = $this->D->id ? $this->D->id : $this->D->_id;
		$result['active'] = $this->D->active;

        $f3->set('SESSION', $result);
	}

	function admin_page($f3){
		$admins = array();
		foreach(\R::find('users', 'admin=1 ORDER BY username') as $user)
			array_push($admins, $user->username);
		$f3->set('admins', implode("\n",$admins));

		show_page($f3, 'admin');
	}

	function admin_post($f3){
		$newAdmins = explode("\n",strtolower(str_replace(chr(13),'',str_replace(' ','',$f3->get('POST.admins')))));

		if(!in_array($f3->get('SESSION.username'), $newAdmins))
			throw new \Exception('Cannot Remove Self from Admin List');

		foreach($newAdmins as $admin){
			if(!strlen($admin)) break;
			$user = \R::findOne('users','username=?', array($admin));
			if(!$user){
				throw new \Exception('User: '.$admin.' cannot be found.');
			}
			$user->admin = 1;
			\R::store($user);
		}

		// Remove Old Admins
		foreach(\R::findAll('users','admin=1') as $currentAdmin){
			if(in_array($currentAdmin->username, $newAdmins)) continue;
			$currentAdmin->admin = 0;
			\R::store($currentAdmin);
		}

		$f3->reroute('@admin');
	}

	// Update Groups and Active Status for All Users
	function update_all($f3){
		set_time_limit(0);
		echo(str_pad('<b>This script takes a long time... Please be patient.</b><br />',4096," "));
		@ob_flush();
		flush();

		$f3->set('POST.username', $f3->get('SESSION.username'));
		$this->authenticate($f3);

		$users = \R::find('users', 'active=1');
		foreach($users as $user){
			$this->D = $user;
			$this->update_user($f3);

			echo(str_pad($user->displayname.' ['.$user->username.'] Updated<br />',4096," "));
			@ob_flush();
			flush();
		}

		echo '<b>All Users Updated. <a href=/admin>Return to Administrator Portal</a>';
	}
}
