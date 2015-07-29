<?php



// Homepage & Tool Index
$f3->route('GET 	@home: 		/', 						'\Controllers\Tools->index');
$f3->route('GET 				/u', 						'\Controllers\Trainings->index');
$f3->route('GET 				/dashboard', 				'\Controllers\Trainings->dashboard');

// Tool & Document Information
$f3->route('GET 				/@name', 					'\Controllers\Tools->info');
$f3->route('GET 				/@name/img', 				'\Controllers\Tools->image_thumbnail');
$f3->route('GET 				/docs/@id', 				'\Controllers\Docs->render');

// User Authentication
$f3->route('GET 				/login', 					'\Controllers\Users->login_form');
$f3->route('POST 				/login', 					'\Controllers\Users->login_post');
$f3->route('GET 				/logout', 					'\Controllers\Users->logout');

// User Pages
$f3->route('GET 				/u/@id', 					'\Controllers\Users->info');

// If User Logged In
if($f3->exists('SESSION')){

	// Add Training Record
	$f3->route('GET|POST 			/@name/train', 				'\Controllers\Trainings->train');

}

// If Administrator Logged In
if($f3->exists('SESSION') && $f3->get('SESSION.admin')){

	// Administrator Portal
	$f3->route('GET 	@admin: 	/admin', 					'\Controllers\Users->admin_page');
	$f3->route('POST 				/admin', 					'\Controllers\Users->admin_post');
	$f3->route('POST 				/admin/updateall', 			'\Controllers\Users->update_all');
	$f3->route('POST 				/admin/import', 			'\Controllers\Trainings->import');
	$f3->route('POST 				/admin/export', 			'\Controllers\Trainings->export');
	$f3->route('POST 				/admin/delete', 			'\Controllers\Trainings->delete');
	$f3->route('POST 				/admin/log', 				'get_log');

	// Manage Toolgroups
	$f3->route('GET 				/toolgroups/new', 			'\Controllers\Toolgroups->edit_form');
	$f3->route('POST 				/toolgroups/new', 			'\Controllers\Toolgroups->edit_post');
	$f3->route('GET 				/toolgroups/@id/edit', 		'\Controllers\Toolgroups->edit_form');
	$f3->route('POST 				/toolgroups/@id/edit', 		'\Controllers\Toolgroups->edit_post');
	$f3->route('GET 				/toolgroups/@id/delete', 	'\Controllers\Toolgroups->delete_form');
	$f3->route('POST 				/toolgroups/@id/delete', 	'\Controllers\Toolgroups->delete_post');

	// Manage Tools
	$f3->route('GET 				/toolgroups/@id/add', 		'\Controllers\Tools->edit_form');
	$f3->route('POST 				/toolgroups/@id/add', 		'\Controllers\Tools->edit_post');
	$f3->route('GET 				/@name/edit', 				'\Controllers\Tools->edit_form');
	$f3->route('POST 				/@name/edit', 				'\Controllers\Tools->edit_post');
	$f3->route('GET 				/@name/delete', 			'\Controllers\Tools->delete_form');
	$f3->route('POST 				/@name/delete', 			'\Controllers\Tools->delete_post');

	// Manage Tool Locations
	$f3->route('GET 	@locations: /locations', 				'\Controllers\Locations->index');
	$f3->route('GET 				/locations/new', 			'\Controllers\Locations->edit_form');
	$f3->route('POST 				/locations/new', 			'\Controllers\Locations->edit_post');
	$f3->route('GET 				/locations/@id/edit', 		'\Controllers\Locations->edit_form');
	$f3->route('POST 				/locations/@id/edit', 		'\Controllers\Locations->edit_post');
	$f3->route('GET 				/locations/@id/delete', 	'\Controllers\Locations->delete_form');
	$f3->route('POST 				/locations/@id/delete', 	'\Controllers\Locations->delete_post');

	// Manage Documents
	$f3->route('GET 				/@name/docs', 				'\Controllers\Docs->index');
	$f3->route('GET 				/@name/docs/new', 			'\Controllers\Docs->edit_form');
	$f3->route('POST 				/@name/docs/new', 			'\Controllers\Docs->edit_post');
	$f3->route('GET 				/docs/@id/edit',			'\Controllers\Docs->edit_form');
	$f3->route('POST 				/docs/@id/edit',			'\Controllers\Docs->edit_post');
	$f3->route('GET 				/docs/@id/delete',			'\Controllers\Docs->delete_form');
	$f3->route('POST 				/docs/@id/delete',			'\Controllers\Docs->delete_post');

	// Images
	$f3->route('GET 				/@name/images', 			'\Controllers\Images->index');
	$f3->route('POST 				/@name/images', 			'\Controllers\Images->new_post');
	$f3->route('GET 				/@name/images/@id/thumb',	'\Controllers\Images->set_thumb');
	$f3->route('GET 				/@name/images/@id/delete',	'\Controllers\Images->delete_post');

}