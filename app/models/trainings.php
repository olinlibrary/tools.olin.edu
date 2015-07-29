<?php
namespace Models;
class Trainings extends \RedBean_SimpleModel {

	function update(){
		$this->bean->level = strtoupper($this->bean->level);

		if($this->bean->instructor === NULL){
			$this->bean->instructor_id = NULL;
			unset($this->bean->instructor);
		}
	}

	// Build an array of training levels indexed by user ids
	function build_lookup($trainings){
		$trainingsLookup = array();
		foreach($trainings as $training){
			if(isset($trainingsLookup[$training->users_id][$training->tools_id][0]))
				$trainingsLookup[$training->users_id][$training->tools_id][0] = $training->tools->higher_training_level($trainingsLookup[$training->users_id][$training->tools_id][0], $training->level);
			else
				$trainingsLookup[$training->users_id][$training->tools_id][0] = $training->level;
			$trainingLevelArray = $training->tools->training_level_array();
			$trainingsLookup[$training->users_id][$training->tools_id][1] = $trainingLevelArray[$trainingsLookup[$training->users_id][$training->tools_id][0]];
		}
		return $trainingsLookup;
	}

}