<?php

namespace Controllers;
class Images extends Base {

	function redirect(){
		return '/'.$this->D->tools->name.'/images';
		return '/';
	}

	function upload_dir(){
		return 'uploads/tools/';
	}

	function index($f3){
		$D = \R::findOne('tools', 'name=?', array($f3->get('PARAMS.name')));
		$D->with('ORDER BY id')->ownImages;
		if($D){
			$exportTmp = \R::exportAll($D);
			$f3->set('data', $exportTmp[0]);
		}else
			$f3->set('data', '');
		show_page($f3, $this->template_name().'.index');
	}

	function new_post($f3){

		$dry = \R::find('images', 'tools_id=?', array($f3->get('POST.tool')));

		// Upload File & Set External Flag
		if(!$this->D->url = $this->upload($f3))
			throw new \Exception('No File!');
		$this->D->tools_id = $f3->get('POST.tool');
		$this->D->thumb = $dry?NULL:1;

		$id = \R::store($this->D);

		if($f3->get('dry')) logger($f3, 'added    '.$this->class_name().', id='.$id);
		else 				logger($f3, 'modified '.$this->class_name().', id='.$id);

		$f3->reroute($this->redirect());
	}

	function set_thumb($f3){
		\R::exec('UPDATE images SET thumb=NULL WHERE tools_id=?', array($this->D->tools->id));
		\R::exec('UPDATE images SET thumb=1 WHERE id=?', array($this->D->id));
		$f3->reroute($this->redirect());
	}

	function delete_post($f3){
		unlink($this->upload_dir().$this->D->url);
		parent::delete_post($f3);
	}

}