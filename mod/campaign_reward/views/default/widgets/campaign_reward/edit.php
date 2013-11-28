<?php
/**
 * campaign_reward widget edit view
 */

// set default value
if (!isset($vars['entity']->num_display)) {
	$vars['entity']->num_display = 4;
}

$params = array(
	'name' => 'params[num_display]',
	'value' => $vars['entity']->num_display,
	'options' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
);
$dropdown = elgg_view('input/select', $params);

?>
<div>
	<?php echo elgg_echo('campaign_reward:numbertodisplay'); ?>:
	<?php echo $dropdown; ?>
</div>