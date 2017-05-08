<?php

namespace Controllers;
class Trainings extends Base {

	protected $model_name = 'tools';
	protected $model_lookup = 'name';

	function index($f3){

        $tools = \R::find('tools');
		logger($f3, 'Tools query executed successfully');

		$D = \R::findAll('toolgroups', 'ORDER BY sort_priority DESC, displayname ASC');
		logger($f3, 'Toolgroups query executed successfully');
		foreach($D as $key=>$element)
			$D[$key]->with("ORDER BY displayname ASC")->ownTools;
		$f3->set('toolgroups', \R::exportAll($D));

		$trainings = \R::find('trainings');
		logger($f3, 'Trainings query executed successfully');
		$f3->set('trainings', $trainings?reset($trainings)->build_lookup($trainings):false);

		$users = \R::find('users', 'active=1 ORDER BY usergroup ASC, displayname ASC');
		logger($f3, 'Users query executed successfully');
        $f3->set('usergroups', $users?reset($users)->group($users):$users);
		logger($f3, 'F3 set usergroups successfully.');

        show_page($f3, 'trainings.index', true);

		logger($f3, 'Leaving trainings index function');
	}

	function dashboard($f3){
		$f3->set('kiosk', true);
		$this->index($f3);
	}

	function import($f3){
        // Authenticate User so we have LDAP Access
		$user = new Users($f3);
		$user->authenticate($f3);

		// Lookup Tool
		if(!$this->D = \R::findOne('tools', 'name=?', array($f3->get('POST.tool'))))
			throw new \Exception('Invalid Tool Name');

		$messages = array();
		$messages[] =  'Training Record Created';
		$messages[] = 'Tool: '.$this->D->name;
		$messages[] = 'Level: '.$f3->get('POST.level');

        $instructor = \R::findOne('users', 'id=? AND active=1', array($f3->get('SESSION.id')));

		// Create Training Records
		$usernames = explode("\n",strtolower(str_replace(chr(13),'',str_replace(' ','',$f3->get('POST.usernames')))));
		foreach($usernames as $username){
			if(strlen($username)){
				try{
					$u = $user->load($f3, $username);
					$record = $this->create_record($f3, $u, $instructor, NULL);
					$messages[] = $f3->get('message');
				}catch(\Exception $e){
					$messages[] = $e->getMessage();
				}
			}
		}

		$f3->set('messages', $messages);
		show_page($f3, 'messages');
	}

	function train($f3){

		// Check if Logged In User is Allowed to Train
		if(!$f3->get('SESSION.active') ||
		   !$f3->get('SESSION.admin') && 
		   !\R::findOne('trainings', 'users_id=? AND tools_id=? AND level=?', array($f3->get('SESSION.id'), $this->D->id, 'T')))
				throw new \Exception('You are not authorized to train for this machine.');


		if($f3->get('POST')){

			// Check Instructor Validity
			if($f3->get('POST.instructor') != $f3->get('SESSION.username'))
				if(!$f3->get('SESSION.admin')) 
					throw new \Exception('Cannot Change Instructor');
			if(!$instructor = \R::findOne('users', 'username=? AND active=1', array($f3->get('POST.instructor'))))
				throw new \Exception('Invalid Instructor Username');
			$f3->set('instructor', $instructor);

			if($f3->exists('POST.username')){

				// Ensure User Checked the Affirmation
			 	if(!$f3->get('POST.affirmation'))
					throw new \Exception('Did not check legal agreement box.');

				// Get Trained User
				$user = new Users($f3);
				$user->authenticate($f3);
				$user->update_user($f3);

				$this->create_record($f3, $user->D, $instructor);
			}
		}

		$f3->set('data.training_levels', json_decode($f3->get('data.training_levels')));
		show_page($f3, 'trainings.train');
	}

	function create_record($f3, $user, $instructor, $timestamp=false){

		// Create Training Record
		$training = \R::dispense('trainings');
		$training->users = $user;
		$training->tools = $this->D;
		$training->instructor = $instructor;
		$training->level = $f3->get('POST.level');

        $this->D->verify_training_level($f3->get('POST.level'));

		// Store Training Record
		\R::begin();
		try{
			$id = \R::store($training);
			\R::commit();
			$f3->set('message', $user->displayname.' has been trained!');
			$f3->set('success', 1);
		}catch(\Exception $e){
            \R::rollback();
			$f3->set('message', $user->displayname.' is already trained on this machine at this level. No training record added.');
			$f3->set('success', 0);
		}

		if($f3->get('success'))
			logger($f3, 'added    '.$this->class_name().', id='.$id);
	}

	function export($f3){
		date_default_timezone_set('America/New_York');
		$filename = 'tmp/trainingrecord '.date("Y-m-d His", time()).'.csv';

		$file = fopen($filename, 'w');
		$trainings = \R::find('trainings');

		// Create Header
		$line = array();
		$line[] = 'Tool';
		$line[] = 'User';
		$line[] = 'Training Level';
		$line[] = 'Instructor';
		$line[] = 'Date';
		fputcsv($file, $line);

		foreach($trainings as $id=>$entry){
			$trainings[$id]->users;
			$trainings[$id]->tools;
			$trainings[$id]->fetchAs('users')->instructor;

			$line = array();
			$line[] = @$entry->tools->name;
			$line[] = @$entry->users->username;
			$line[] = @$entry->level;
			$line[] = @$entry->instructor->username;
			$line[] = @$entry->timestamp?date("Y-m-d", $entry->timestamp):'';
			fputcsv($file, $line);
		}

		fclose($file);

		// Send File to Browser
		$web = \Web::instance();
		$web->send($filename);
	}

	function delete($f3){
		if(!$user = \R::findOne('users', 'username=?', array(strtolower($f3->get('POST.username')))))
			throw new \Exception('Could not find user');
		if(!$tool = \R::findOne('tools', 'name=?', array(strtolower($f3->get('POST.tool')))))
			throw new \Exception('Could not find tool');
		if(!$records = \R::find('trainings', 'users_id=? AND tools_id=? AND level=?', array($user->id, $tool->id, strtoupper($f3->get('POST.level')))))
			throw new \Exception('Could not any training records for this user on this tool at this level.');

		\R::trashAll($records);

		$f3->set('messages', array('All Records of User '.$user->username.' on Tool '.$tool->name.' at Level '.strtoupper($f3->get('POST.level')).' deleted.'));
		show_page($f3, 'messages');
	}

}
