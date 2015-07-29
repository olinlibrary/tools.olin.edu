<?php

namespace Controllers;
class Base {

	protected $D;
	protected $model_lookup = 'id';

	// Load Entry if Exists
	function __construct($f3){
		if($f3->exists('PARAMS.'.$this->model_lookup)){
			$this->D = \R::findOne($this->model_name(), $this->model_lookup.'=?', array($f3->get('PARAMS.'.$this->model_lookup)));
			if(!$this->D) $f3->error(404);;
			$exportTmp = \R::exportAll($this->D, true);
			$f3->set('data', $exportTmp[0]);
			$f3->set('dry',0);

		}else{
			$this->D = \R::dispense($this->model_name());
			$f3->set('dry',1);
		}
	}

	function model_name(){
		if(isset($this->model_name))
			return $this->model_name;
		return $this->class_name();
	}

	function template_name(){
		if(isset($this->template_name))
			return $this->template_name;
		return $this->class_name();
	}

	function class_name(){
		$classes = explode('\\', get_called_class());
		return strtolower($classes[1]);
	}

	function redirect(){
		return isset($this->redirect) ? $this->redirect : '@home';
	}

	function check_empty($f3){
		return true;
	}
	
	function index($f3){
		$D = \R::find($this->model_name(), 'ORDER BY displayname ASC');
		if($D){
			$f3->set('data', \R::exportAll($D));
		}else
			$f3->set('data', '');
		show_page($f3, $this->template_name().'.index');
	}
	
	function info($f3){
		show_page($f3, $this->template_name().'.info');
	}
	
	function edit_form($f3){
		$f3->set('empty',$this->check_empty($f3));
		show_page($f3, $this->template_name().'.edit');
	}
	
	function delete_form($f3){
		if(!$this->check_empty($f3)) throw new \Exception('Not Empty');

		show_page($f3, $this->template_name().'.delete');
	}

	function edit_post($f3){
		$this->D->import($f3->get('POST'));
		$id = \R::store($this->D);

		if($f3->get('dry')) logger($f3, 'added    '.$this->class_name().', id='.$id);
		else 				logger($f3, 'modified '.$this->class_name().', id='.$id);

		$f3->reroute($this->redirect());
	}

	function delete_post($f3){
		if(!$this->check_empty($f3)) 
			throw new \Exception('Not Empty');

		\R::trash($this->D);

		logger($f3, 'deleted  '.$this->class_name().', id='.$f3->get('PARAMS.id'));

		$f3->reroute($this->redirect());
	}

	function upload_dir(){
		return 'uploads/'.$this->template_name().'/';
	}

	function upload($f3, $type='DOC'){
		$f3->set('temp.file_type', $type); // Move into Globals

		// Handle Upload
		\Web::instance()->receive(
			function($file) use($f3) {
				if(!in_array($file['type'], $f3->get('ALLOWED_'.$f3->get('temp.file_type'))))
					throw new \Exception('File Type Not Allowed');

				$exploded = explode('.',$file['name']);
				$f3->set('file.extension',end($exploded));
				$f3->set('file.name',$file['name']);
				return true;
			}, true);

		// Move Uploaded File
		if($f3->exists('file.name')){
			$filename = md5(microtime()).'.'.$f3->get('file.extension');
			sleep(1);
			rename($f3->get('file.name'), $this->upload_dir().$filename);

			logger($f3, 'upload   '.$this->class_name().', filename='.$f3->get('file.name'));
			return $filename;
		}

		return false;
	}

}