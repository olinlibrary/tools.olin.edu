<?php
namespace Models;
class Tools extends \RedBean_SimpleModel {

	private $training_level_array = false;
	private $training_level_ranking = false;

	function update(){
		// Name
		$this->bean->name = strtolower($this->bean->name);
		$this->bean->name = preg_replace('/\s+/', '', $this->bean->name);
		$reservedNames = array('dashboard','docs','login','logout','u','admin','toolgroups','locations','old');
		if(in_array($this->bean->name, $reservedNames))
			throw new \Exception('Reserved Name!');
	}

	function training_level_array(){
		if(!$this->training_level_array){
			$this->training_level_array = array();
			foreach(json_decode($this->bean->training_levels) as $level)
				$this->training_level_array[$level[0]] = $level[1];
		}
		return $this->training_level_array;
	}

	function verify_training_level($check){
		$levels = array();
		$check =  strtoupper($check);
		foreach($this->training_level_array() as $key=>$item)
			array_push($levels, $key);
		if(!in_array($check, $levels))
			throw new \Exception('Invalid Training Level');
	}

	function training_level_rank($check){
		if(!$this->training_level_ranking){
			$this->training_level_ranking = array();
			foreach(json_decode($this->bean->training_levels) as $key=>$level)
				$this->training_level_ranking[$level[0]] = $key;
		}
		if(isset($this->training_level_ranking[$check]))
			return $this->training_level_ranking[$check];
		return -1;
	}

	function higher_training_level($a, $b){
		if($this->training_level_rank($a) > $this->training_level_rank($b))
			return $a;
		return $b;
	}

}