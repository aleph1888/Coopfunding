<?php

/**
 * Gatekeeper for moderation plugin. Only for admins.
 */
function moderator_gate_keeper() {
	if (elgg_is_admin_logged_in()){
		return true;
	}else {
		FORWARD('','404');
	}

}

/**
 * Adds a toggle to extra menu for switching between list and gallery views
 */
function moderation_register_toggle() {
	set_input('list_type', get_input('list_type', 'gallery'));

	$url = elgg_http_remove_url_query_element(current_page_url(), 'list_type');

	if (get_input('list_type', 'list') == 'list') {
		$list_type = "gallery";
		$icon = elgg_view_icon('grid');
	} else {
		$list_type = "list";
		$icon = elgg_view_icon('list');
	}

	if (substr_count($url, '?')) {
		$url .= "&list_type=" . $list_type;
	} else {
		$url .= "?list_type=" . $list_type;
	}

	elgg_register_menu_item('extras', array(
		'name' => 'file_list',
		'text' => $icon,
		'href' => $url,
		'title' => elgg_echo("file:list:$list_type"),
		'priority' => 1000,
	));

	return true;

}

/**
* Returns the revision on the top of the record of $entity (project or fundcampaign) only if it is "in_progress" state, which is: waiting for being "commited" by admin; or null if none.
*/
function moderation_get_last_revision($entity) {
	
	if ($entity) {
		return current(elgg_get_entities_from_metadata( array(
			'type' => 'object',
			'subtype' => 'revision',
			'container_guid' => $entity->guid,
			'metadata_name' => 'state',
			'metadata_value' => array("in_progress", "request"),
			'limit' => 1
			)));
	}
	return null;

}

/**
* Calls to elgg_get_entities_from_metadata to return all revision objects related to $entity (which can be either project or fundcampaign.
*/
function moderation_get_revisions($entity) {
	$revisions = elgg_get_entities_from_metadata( array(
		'type' => 'object',
		'subtype' => 'revision',
		'container_guid' => $entity->guid,
		));
	return $revisions;

}

/*
* Get string as the output representation of any field, if there is uncommited changes in $revision then show both fields, just to admin can check. By default, changes will be loaded in output field so admin only has to edit if needs to stash changes.
*/
function moderation_get_field ($revision, $entity_type, $fieldname, $fieldtype, $fieldvalue) {
	
	$line_break = '<br />';

	$valNew = $revision->$fieldname;
	if ($valNew && ($valNew != $fieldvalue)) {
		$class = "moderation-edited";
	} else {
		$valNew = $fieldvalue;
	}

	$output = "<div><label class='{$class}'>";
	$output .= elgg_echo("{$entity_type}:{$fieldname}");
	$output .= "</label>$line_break";

	if ($class) {
		$output .= elgg_view("output/{$fieldtype}", array(
			'name' => $fieldname,
			'value' => $fieldvalue
		));
	}

	$output .=  elgg_view("input/{$fieldtype}", array(
			'name' => $fieldname,
			'value' => $valNew
		));
	$output .= '</div>';

	return $output;
}

/*
* Get string as the output representation of icon, if there is uncommited icon in $revision then show both in medium size; or nothing if not. By default, $revision icon will be saved. Click a checkbox to discard $revision icon.
*/
function moderation_get_field_icon ($entity, $revision) {

	$filename = "{$entity->getSubtype()}icon/{$entity->guid}/medium/{$entity->icontime}.jpg";

	$img_params = array(
			'src' => $filename,
			'alt' => $title,
			'width' => '300'
	);
	$output = elgg_view('output/img', $img_params);

	$filename = "{$entity->getSubtype()}icon/{$entity->guid}revision{$revision->guid}/medium/{$revision->icontime}.jpg";
	$img_params = array(
			'src' => $filename,
			'alt' => $title,
			'width' => '300'
	);
	$output .= elgg_view('output/img', $img_params);

	if (elgg_is_admin_logged_in()){
		$output .= elgg_echo("moderation:stash");
		$output .= elgg_view('input/checkbox', array('name' => 'discard_icon'));
	}
	return $output;

}

/*
* Returns a button for user's to request entity state from state("in_progress") to state("commited"); or false.
*/
function moderation_get_request_user_button ($entity_guid) {

	if (!elgg_is_admin_logged_in()) {
		$entity = get_entity($entity_guid);
		$revision = moderation_get_last_revision($entity);
		if ($entity->state == "in_progress" || $revision ->state == "in_progress") {			
			$request_guid = $entity_guid;
			if ($revision) { $request_guid = $revision->guid;}
			$request_url = 'action/moderation/request?guid=' . $request_guid;
			return elgg_view('output/confirmlink', array(
				'text' => elgg_echo('moderation:request'),
				'href' => $request_url,
				'confirm' => elgg_echo('moderation:requestwarning'),
				'class' => 'elgg-button elgg-button-save float-alt',
			));
		}
	}

	return false;

}

/** Manage $entity's saving icon (project or fundcampaing). Updates $entity->icontime or $revision->icontime.
* $ACTION:
* 	NEW --> upload icon.
* 	REVISION --> upload ico but save it as a revison with name: {$entity_guid}revision{$revision_guid}.jpg';
* 	COMMIT -->
* 		if $has_uploaded_icon then
* 			upload icon
* 			DESACTIVATED: delete if exists {$entity_guid}'revision{$revision_guid}.jpg';
* 		else
* 			copy {$entity_guid}revision{$revision_guid}.jpg' to {$entity_guid}.jpg
* $discard_icon:
* 	Admin do not allow change icon to revision one.
*/
function moderation_save_icon ($params) {
	$entity = $params['entity'];
	$entity_type = $params['plugin_name'];
	$action = $params['action'];
	$revision = $params['revision'];
	$discard_icon = $params['discard_icon'];;

	$has_uploaded_icon = (!empty($_FILES['icon']['type']) && substr_count($_FILES['icon']['type'], 'image/'));

	if ($has_uploaded_icon || $action == "commit") {

		switch ($action){
			case "new":
				$prefix_to_upload = "{$entity_type}/" . $entity->guid;
				$entity->icontime = time();
				break;
			case "revision":
				$prefix_to_upload = "{$entity_type}/{$entity->guid}revision{$revision->guid}";
				$revision->icontime = time();
				break;
			case "commit":
				if ($has_uploaded_icon) {
					$prefix_to_upload = "{$entity_type}/{$entity->guid}";
					$entity->icontime = time();
					//$prefix_to_del = "{$entity_type}/" . $entity->guid . "revision{$revision}";
				} else {
				
					if ($discard_icon) {
						$prefix_to_del = "{$entity_type}/{$entity->guid}revision{$revision->guid}";	
					}else {
						if ($revision){
							$prefix_copy_to = "{$entity_type}/{$entity->guid}";
							$prefix_copy_from = "{$entity_type}/{$entity->guid}revision{$revision->guid}";
							$entity->icontime = time();						
						}
					}
				}
				break;
			default:
				break;
		}

		if ($prefix_to_upload) {
			elgg_load_library("elgg:{$entity_type}");
			
			$icon_sizes = elgg_get_config("group_icon_sizes");

			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename($prefix_to_upload . ".jpg");
			$filehandler->open("write");
			$filehandler->write(get_uploaded_file('icon'));
			$filehandler->close();
			$filename = $filehandler->getFilenameOnFilestore();

			$sizes = array('tiny', 'small', 'medium', 'large');
			$thumbs = array();
			foreach ($sizes as $size) {
				$thumbs[$size] = call_user_func("moderation_get_resized_and_cropped_image_from_existing_file", $filename,
					$icon_sizes[$size]['w'],
					$icon_sizes[$size]['h']
				);
			}

			if ($thumbs['tiny']) { // just checking if resize successful
				$thumb = new ElggFile();
				$thumb->owner_guid = $entity->owner_guid;
				$thumb->setMimeType('image/jpeg');

				foreach ($sizes as $size) {
					$thumb->setFilename("{$prefix_to_upload}{$size}.jpg");

					$thumb->open("write");
					$thumb->write($thumbs[$size]);
					$thumb->close();
				}
			}
		}

		if ($prefix_copy_from) {
			$sizes = array('', 'tiny', 'small', 'medium', 'large');
			foreach ($sizes as $size) {
				$filehandler = new ElggFile();
				$filehandler->setFilename("{$prefix_copy_from}{$size}.jpg");
				$filehandler->owner_guid = $entity->owner_guid;
				$from = $filehandler->getFilenameOnFilestore();

				$filehandler = new ElggFile();
				$filehandler->setFilename("{$prefix_copy_to}{$size}.jpg");
				$filehandler->owner_guid = $entity->owner_guid;
				$to = $filehandler->getFilenameOnFilestore();

				copy($from, $to);
			}
		}

		if ($prefix_to_del) {
			$filehandler = new ElggFile();
			$filehandler->setFilename($prefix_to_del);
			if ($filehandler->open("read")) {
				$path = $filehandler->getFilenameOnFilestore();
				$sizes = array('', 'tiny', 'small', 'medium', 'large');
				foreach ($sizes as $size) {
					unlink("$path/{$prefix_to_del}{$size}.jpg");
				}
			}
		}		
	}
	return true;

}


/**
 * Gets the jpeg contents of the resized and cropped version of an already
 * uploaded image (Returns false if the file was not an image)
 *
 * @param string $input_name The name of the file on the disk
 * @param int    $new_width   The desired width of the resized image
 * @param int    $new_height  The desired height of the resized image
 * 
 * @return false|mixed The contents of the resized image, or false on failure
 */
function moderation_get_resized_and_cropped_image_from_existing_file($input_name, $new_width, $new_height) {

	// Get the size information from the image
	$imgsizearray = getimagesize($input_name);
	if ($imgsizearray == FALSE) {
		return FALSE;
	}

	$source_width = $imgsizearray[0];
	$source_height = $imgsizearray[1];

	$source_aspect_ratio = $source_width / $source_height;
	$new_aspect_ratio = $new_width / $new_height;

	if ($new_width > $source_width) {
		$new_width = $source_width;
		$new_height = $source_width / $new_aspect_ratio;
	}
	if ($new_height > $source_height) {
		$new_height = $source_height;
		$new_width = $source_height * $new_aspect_ratio;
	}

	$accepted_formats = array(
		'image/jpeg' => 'jpeg',
		'image/pjpeg' => 'jpeg',
		'image/png' => 'png',
		'image/x-png' => 'png',
		'image/gif' => 'gif'
	);

	// make sure the function is available
	$load_function = "imagecreatefrom" . $accepted_formats[$imgsizearray['mime']];
	if (!is_callable($load_function)) {
		return FALSE;
	}

	// load original image
	$original_image = $load_function($input_name);
	if (!$original_image) {
		return FALSE;
	}

	if ($source_aspect_ratio > $new_aspect_ratio) {
		$temp_height = $new_height;
		$temp_width = (int) ($new_height * $source_aspect_ratio);
	} else {
		$temp_width = $new_width;
		$temp_height = (int) ($new_width / $source_aspect_ratio);
	}

	// Resize the image into a temporary GD image
	$temp_image = imagecreatetruecolor($temp_width, $temp_height);
	$temp_rtn_code = imagecopyresampled(
		$temp_image,
		$original_image,
		0, 0,
		0, 0,
		$temp_width, $temp_height,
		$source_width, $source_height
	);
	if (!$temp_rtn_code) {
		return FALSE;
	}

	// Copy cropped region from temporary image into the desired GD image
	$x0 = ($temp_width - $new_width) / 2;
	$y0 = ($temp_height - $new_height) / 2;
	$new_image = imagecreatetruecolor($new_width, $new_height);
	$rtn_code = imagecopy(
		$new_image,
		$temp_image,
		0, 0,
		$x0, $y0,
		$new_width, $new_height
	);
	if (!$rtn_code) {
		return FALSE;
	}

	// grab a compressed jpeg version of the image
	ob_start();
	imagejpeg($new_image, NULL, 90);
	$jpeg = ob_get_clean();

	imagedestroy($new_image);
	imagedestroy($temp_image);
	imagedestroy($original_image);

	return $jpeg;
}