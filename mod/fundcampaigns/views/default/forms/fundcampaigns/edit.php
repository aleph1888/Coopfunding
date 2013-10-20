<?php
/**
 * Campaigns edit form
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

// only extract these elements.
$name = $alias = $vis = $entity = null;
extract($vars, EXTR_IF_EXISTS);

$moderate = elgg_is_active_plugin('moderation');
if ($moderate) {
	elgg_load_library('elgg:moderation');	
	$revision = get_moderation_last_revision($entity);	
}
?>

?>
<div>
	<label><?php echo elgg_echo("fundcampaigns:icon"); ?></label><br />
	<?php echo elgg_view("input/file", array('name' => 'icon')); ?>
</div>
<div>
	<?php

		if ($moderate) {
			echo moderation_get_field ($revision, 'fundcampaigns', 'name', 'text', $name);
		} else {
			echo "<div><label>";
			echo elgg_echo("fundcampaigns:name");
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
			echo moderation_get_field ($revision, 'fundcampaigns', 'alias', 'text', $alias);
		} else {
			echo "<div><label>";
			echo elgg_echo("fundcampaigns:alias");
			echo "</label><br />";
			echo elgg_view("input/text", array(
				'name' => name,
				'value' => $alias
			));
			echo '</div>';
		}
	?>
</div>
<?php

$fundcampaign_profile_fields = elgg_get_config('fundcampaign');
if ($fundcampaign_profile_fields > 0) {
	foreach ($fundcampaign_profile_fields as $shortname => $valtype) {
		$line_break = '<br />';
		if ($valtype == 'longtext') {
			$line_break = '';
		}

		if ($moderate) {
			echo moderation_get_field ($revision, 'fundcampaigns', $shortname, $valtype, elgg_extract($shortname, $vars));
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

if ($entity && !$entity->is_active) {
    $entity->is_active = "YES";
}
    
$active_options = array(
		YES => elgg_echo('fundcampaigns:active'),
		NO => elgg_echo("fundcampaigns:inactive")
);
?>

<div>
	<label>
			<?php echo elgg_echo('fundcampaigns:is_active'); ?><br />
			<?php echo elgg_view('input/dropdown', array(
				'name' => 'is_active',
				'value' =>  $entity->is_active,
				'options_values' => $active_options,
			));
			?>
	</label>
</div>

<?php

if (elgg_is_admin_logged_in()) {
	$access_options = array(
		ACCESS_PRIVATE => elgg_echo('fundcampaigns:access:fundcampaign'),
		ACCESS_PUBLIC => elgg_echo("PUBLIC")
	);
?>

<div>
	<label>
			<?php echo elgg_echo('fundcampaigns:visibility'); ?><br />
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

$tools = elgg_get_config('fundcampaigns_tool_options');
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
				elgg_echo('fundcampaigns:yes') => 'yes',
				elgg_echo('fundcampaigns:no') => 'no',
			),
		));
		?>
</div>
<?php
	}
}
?>
<div class="elgg-foot">
<?php

	echo elgg_view('input/hidden', array(
		'name' => 'project',
		'value' => get_input('project'),
	));



if ($entity) {
	echo elgg_view('input/hidden', array(
		'name' => 'fundcampaign_guid',
		'value' => $entity->getGUID(),
	));
}

echo elgg_view('input/submit', array('value' => elgg_echo('save')));

if ($entity) {
	$delete_url = 'action/fundcampaigns/delete?guid=' . $entity->getGUID();
	echo elgg_view('output/confirmlink', array(
		'text' => elgg_echo('fundcampaigns:delete'),
		'href' => $delete_url,
		'confirm' => elgg_echo('fundcampaigns:deletewarning'),
		'class' => 'elgg-button elgg-button-delete float-alt',
	));
}

//Moderation user button to request publish the project
if ($entity) {
	if (elgg_is_active_plugin("moderation")) {
		if (elgg_is_admin_logged_in()) {
			echo moderation_get_request_admin_button ($entity->getGUID());
		}else {
			echo moderation_get_request_user_button ($entity->getGUID());
		}		
	}	
}
?>
</div>
