<?php
/**
 * Project edit form
 * 
 * @package Coopfunding
 * @subpackage Projects
 */

// only extract these elements.
$name = $alias = $vis = $entity = null;
extract($vars, EXTR_IF_EXISTS);

$moderate = elgg_is_active_plugin('moderation');
if ($moderate) {
	elgg_load_library('elgg:moderation');	
	$revision = moderation_get_last_revision($entity);
	if ($revision &&  $revision->icontime) {
		$is_ico_changed = $revision->icontime != $entity->icontime;
	}	
}
?>

<div>

	<label><?php echo elgg_echo("projects:icon"); ?></label><br />
	<?php 
		echo elgg_view("input/file", array('name' => 'icon')); 
		if ($moderate && $is_ico_changed) {
			echo moderation_get_field_icon ($entity, $revision);
		}
	?>

</div>
<div>
	<?php
		if ($moderate) {
			echo moderation_get_field ($revision, 'projects', 'name', 'text', $name);
		} else {
			echo "<div><label>";
			echo elgg_echo("projects:name");
			echo "</label><br />";
			echo elgg_view("input/text", array(
				'name' => name,
				'value' => $name
			));
			echo '</div>';
		}
	?>
</div>
<div>
	<?php 
		if ($moderate) {
			echo moderation_get_field ($revision, 'projects', 'alias', 'text', $alias);
		}else {
			echo "<div><label>";
			echo elgg_echo("projects:alias");
			echo "</label><br />";
			echo elgg_view("input/text", array(
				'name' => alias,
				'value' => $alias
			));
			echo '</div>';
		}		
	?>
</div>
<?php

$project_profile_fields = elgg_get_config('project');
if ($project_profile_fields > 0) {
	foreach ($project_profile_fields as $shortname => $valtype) {
		$line_break = '<br />';
		if ($valtype == 'longtext') {
			$line_break = '';
		}

		if ($moderate) {
			echo moderation_get_field ($revision, 'projects', $shortname, $valtype, elgg_extract($shortname, $vars));
		} else{
			echo "<div><label>";
			echo elgg_echo("projects:{$shortname}");
			echo "</label>$line_break";
			echo elgg_view("input/{$valtype}", array(
				'name' => $shortname,
				'value' => elgg_extract($shortname, $vars)
			));
			echo '</div>';
		}
	}
}

//access can only be controlled by admin
if (elgg_is_admin_logged_in()) {
	$access_options = array(
		ACCESS_PRIVATE => elgg_echo('projects:access:project'),
		ACCESS_PUBLIC => elgg_echo("PUBLIC")
	);
	?>

	<div>
		<label>
			<?php echo elgg_echo('projects:visibility'); ?><br />
			<?php echo elgg_view('input/access', array(
					'name' => 'vis',
					'value' =>  $vis,
					'options_values' => $access_options,
					));
			?>
		</label>
	</div>
<?php
}

#TODO
/*$tools = elgg_get_config('project_tool_options');
if ($tools) {
	usort($tools, create_function('$a,$b', 'return strcmp($a->label,$b->label);'));
	foreach ($tools as $project_option) {
		$project_option_toggle_name = $project_option->name . "_enable";
		$value = elgg_extract($project_option_toggle_name, $vars);
		?>	
		<div>
			<label>
				<?php echo $project_option->label; ?><br />
			</label>
				<?php echo elgg_view("input/radio", array(
					"name" => $project_option_toggle_name,
					"value" => $value,
					'options' => array(
						elgg_echo('projects:yes') => 'yes',
						elgg_echo('projects:no') => 'no',
					),
				));
				?>
		</div>
		<?php
	}
}*/
?>
<div class="elgg-foot">
<?php

if ($entity) {
	echo elgg_view('input/hidden', array(
		'name' => 'project_guid',
		'value' => $entity->getGUID(),
	));
}

if (elgg_is_admin_logged_in() || $entity->state != "request") {
	echo elgg_view('input/submit', array('value' => elgg_echo('save')));
}

$showDeleteButton = elgg_is_active_plugin ("moderation") && elgg_is_admin_logged_in();
if ($entity && $showDeleteButton) {
	if (elgg_is_admin_logged_in()) {
		$delete_url = 'action/projects/delete?guid=' . $entity->getGUID();
		echo elgg_view('output/confirmlink', array(
			'text' => elgg_echo('projects:delete'),
			'href' => $delete_url,
			'confirm' => elgg_echo('projects:deletewarning'),
			'class' => 'elgg-button elgg-button-delete float-alt',
		));
	}
}

/*
* Moderation user button to request publish the project
*/
if ($entity) {
	if (elgg_is_active_plugin("moderation")) {
		if (!elgg_is_admin_logged_in()) {
			echo moderation_get_request_user_button ($entity->getGUID());		
		}
	}	
}
?>
</div>
