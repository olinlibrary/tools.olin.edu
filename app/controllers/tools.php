<?php

namespace Controllers;
class Tools extends Base {

	protected $model_lookup = 'name';

	function check_empty($f3){
		return !(bool) $this->D->countOwn('trainings');
	}

	function redirect(){
		return '/'.$this->D->name;
	}

	function index($f3){
		$D = \R::findAll('toolgroups', 'ORDER BY sort_priority DESC, displayname ASC');
		foreach($D as $key=>$element)
			$D[$key]->with("ORDER BY displayname ASC")->ownTools;
        $f3->set('data', \R::exportAll($D));

		show_page($f3, 'tools.index');
    }

	function info($f3){
		$this->D->with('ORDER BY displayname ASC')->ownDocs;

		// Render Notes as Markdown
		$md = \Markdown::instance();
		$f3->set('data.notes', $md->convert($this->D->notes));
		$f3->set('training_levels', $this->D->training_level_array());

		$images = \R::find('images', 'tools_id=? ORDER BY thumb DESC, id ASC', array($this->D->id));
		if($images) $f3->set('images', $images);
		$f3->set('multiple_images', count($images) > 1);

		// Build Array of Training Records
		$trainings = \R::find('trainings', 'tools_id=?', array($this->D->id));
		$f3->set('trainings', $trainings?reset($trainings)->build_lookup($trainings):false);

		// Build Array of All Active Users in Groups
		$users = \R::find('users', 'active=1 ORDER BY usergroup ASC, displayname ASC');
		$f3->set('usergroups', $users?reset($users)->group($users):false);

		// Check if User can Train
		$f3->set('can_train', $f3->get('SESSION.active') &&
		   $f3->get('SESSION.admin') || 
		   \R::findOne('trainings', 'users_id=? AND tools_id=? AND level=?', array($f3->get('SESSION.id'), $this->D->id, 'T')));
        parent::info($f3);
	}

	function edit_form($f3){

		// Get Toolgroup Options
		$D = \R::findAll('toolgroups', 'ORDER BY sort_priority DESC, displayname ASC');
		$f3->set('toolgroups', \R::exportAll($D));

		// Get Training Levels
		$f3->set('training_levels', $f3->exists('data.training_levels') ? json_decode($f3->get('data.training_levels')) : $f3->get('TRAINING_LEVELS'));

		// Get Location Options
		$D = \R::findAll('locations', 'ORDER BY displayname ASC');
		$f3->set('locations', \R::exportAll($D));
		foreach($f3->get('locations') as $i=>$location)
			$f3->set('locations.'.$i.'.active', isset($this->D->sharedLocationsList[$locations['id']]));

		parent::edit_form($f3);
	}

	function edit_post($f3){

		// Update Tool Locations
		$this->D->sharedLocationsList = array();
		if($f3->exists('POST.locations'))
			foreach($f3->get('POST.locations') as $id)
				$this->D->sharedLocationsList[] = \R::load('locations', $id);
		$f3->clear('POST.locations');

		$this->D->import($f3->get('POST'));
		$this->D->training_levels or $this->D->training_levels = json_encode($f3->get('TRAINING_LEVELS'));

		\R::begin();
		try{
			$id = \R::store($this->D);
			\R::commit();
		}catch(\Exception $e){
			\R::rollback();
			if($e->getSQLState() == 23000);
				throw new \Exception($this->D->name.' is not a unique name.');
			throw new \Exception();
		}

		if($f3->get('dry')) logger($f3, 'added    '.$this->class_name().', id='.$id);
		else 				logger($f3, 'modified '.$this->class_name().', id='.$id);

		$f3->reroute($this->redirect());
	}

	function delete_post($f3){
		foreach($this->D->ownImages as $image)
			@unlink($this->upload_dir().$image->url);

		parent::delete_post($f3);
	}

	function image_thumbnail($f3){
		// Get Image
		$image = \R::findOne('images', 'tools_id=? AND thumb=1', array($this->D->id));
		if($image){
			if(!file_exists($cached = 'tmp/thumb/'.$image->url)){
				if(!file_exists($filename = $this->upload_dir().$image->url))
					throw new \Exception('Could not find image');
				$img = new \Image($filename);
				$img->resize(200, 200);
				file_put_contents($cached, $img->dump('jpeg'));
			}
			$web = \Web::instance();
			$web->send($cached, null, 0, false);
		}else{
			$img = new \Image();
			$img->identicon($this->D->name, 200);
			$img->render('jpeg');
		}
    }
}
