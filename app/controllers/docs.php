<?php

namespace Controllers;
class Docs extends Base {

	function redirect(){
		return '/'.$this->D->tools->name.'/docs';
		return '/';
	}

	function index($f3){
		$D = \R::findOne('tools', 'name=?', array($f3->get('PARAMS.name')));
		if($D){
			$exportTmp = \R::exportAll($D);
			$f3->set('data', $exportTmp[0]);
		}else
			$f3->set('data', '');
		show_page($f3, $this->template_name().'.index');
	}

	function docs_new($f3){
		$f3->set('dry', 1);
		$f3->set('data.tools', $f3->get('data'));
		show_page($f3, 'docs.edit');
	}
	
	// Render Uploaded Document
	function render($f3){
		$web = \Web::instance();
		$web->send('uploads/'.$this->template_name().'/'.$this->D->url, null, 0, false);
	}

	function edit_form($f3){
		// Get Tool Information
		if($f3->get('dry')){
			$this->model_name = 'tools';
			$this->model_lookup = 'name';
			$this->__construct($f3);
			$f3->set('data.tools', $f3->get('data'));
			$f3->set('dry', 1);
		}

		parent::edit_form($f3);
	}

	function edit_post($f3){
		$this->D->import($f3->get('POST'));

		// Upload File & Set External Flag
		if($this->D->external = !(bool)($this->D->url = $this->upload($f3)))
			$this->D->url = $f3->get('POST.url');

		$id = \R::store($this->D);

		if($f3->get('dry')) logger($f3, 'added    '.$this->class_name().', id='.$id);
		else 				logger($f3, 'modified '.$this->class_name().', id='.$id);

		$f3->reroute($this->redirect());
	}

	function delete_post($f3){
		$this->D->external or unlink($this->upload_dir().$this->D->url);
		parent::delete_post($f3);
	}

}