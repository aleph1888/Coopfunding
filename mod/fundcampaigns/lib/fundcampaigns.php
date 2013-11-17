<?php
/**
 * Fundcampaigns function library
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

/**
 * Get project entity from its alias
 */
function fundcampaigns_get_from_alias($alias) {

	return current(elgg_get_entities_from_metadata(array(
		'type' => 'group',
		'subtype' => 'fundcampaign',
		'metadata_name' => 'alias',
		'metadata_value' => $alias,
		'limit' => 1,
	)));

}

/**
 * Prepares variables for the project edit form view.
 *
 * @param mixed $campaign ElggGroup or null. If a project, uses values from the project.
 * @return array
 */
function fundcampaigns_prepare_form_vars($fundcampaign = null) {

	$values = array(
		'name' => '',
		'alias' => '',
		'vis' => null,
		'guid' => null,
		'entity' => null,
		'num_periods' => elgg_get_plugin_setting("num_periods", "fundcampaigns"),
		'periods_duration' => elgg_get_plugin_setting("periods_duration", "fundcampaigns"),
	);

	// handle customizable profile fields
	$fields = elgg_get_config('fundcampaigns');

	if ($fields) {
		foreach ($fields as $name => $type) {
			if (!isset($values[$name])) {
				$values[$name] = '';
			}
		}
	}

	// handle tool options
	$tools = elgg_get_config('fundcampaigns_tool_options');
	if ($tools) {
		foreach ($tools as $fundcampaign_option) {
			$option_name = $fundcampaign_option->name . "_enable";
			$values[$option_name] = $fundcampaign_option->default_on ? 'yes' : 'no';
		}
	}

	// get current fundcampaigns settings
	if ($fundcampaign) {
		foreach (array_keys($values) as $field) {
			if (isset($fundcampaign->$field)) {
				$values[$field] = $fundcampaign->$field;
			}
		}

		if ($fundcampaign->access_id != ACCESS_PUBLIC && $fundcampaign->access_id != ACCESS_LOGGED_IN) {
			// fundcampaign only access - this is done to handle access not created when fundcampaign is created
			$values['vis'] = ACCESS_PRIVATE;
		} else {
			$values['vis'] = $fundcampaign->access_id;
		}

		$values['entity'] = $fundcampaign;
	}

	// get any sticky form settings
	if (elgg_is_sticky_form('fundcampaigns')) {
		$sticky_values = elgg_get_sticky_values('fundcampaigns');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('fundcampaigns');

	return $values;

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
/**
 * Could reuse projects library to do this!!!!!
 */
function fundcampaigns_get_resized_and_cropped_image_from_existing_file($input_name, $new_width, $new_height) {

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

function fundcampaigns_is_active_campaign ($fundcampaign) {
	$date = date('Y-m-d');
	return $fundcampaign->is_active && $date > $fundcampaign->start_date;
}

function fundcampaigns_get_active_campaign ($guid = 0) {

	return current(elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'fundcampaign',
		'container_guid' => $guid,
		'metadata_name' => 'is_active',
		'metadata_value' => true,
		'limit' => 1,
	)));

}
