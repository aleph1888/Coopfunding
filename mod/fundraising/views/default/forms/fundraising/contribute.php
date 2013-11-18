<?php

$methods = fundraising_get_methods();

if (empty($methods)) {
	echo elgg_echo('fundraising:contribute:nomethod');
	return;
}
?>
<div>
<?php
echo elgg_view('input/text', array(
	'name' => 'amount',
	'value' => $vars['amount'],
	'placeholder' => elgg_echo('fundraising:amount'),
	'autofocus' => true,
));
?>
</div>
<div class="elgg-footer">
<?php
echo elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $vars['entity']->guid,
));
echo elgg_view('input/hidden', array(
	'name' => 'reward_guid',
	'value' => $vars['reward_guid'],
));


echo '<div>';
echo elgg_echo("fundraising:message");
echo '</div>';


foreach (fundraising_get_methods() as $method) {

	$url = "fundraising/{$method}";
	elgg_extend_view($url, "fundraising-{$method}/contribute");
	if (elgg_view_exists($url)){
		$buttons .= elgg_view('input/button', array(
			'name' => "fundraising_{$method}_contribute_button",
			'id' => "fundraising_{$method}_contribute_button",
	                'value' => elgg_echo('fundraising:contribute:button:method', array($method)), #change this literal will disconfigure in fundraising/actions/contribute.php
			'class' => "fundraising-{$method}-contribute-button"
			));		
		$forms .= elgg_view($url, $vars);	

	} else {
	        $buttons .= elgg_view('input/submit', array(
			'name' => 'method',
	                'value' => elgg_echo('fundraising:contribute:button:method', array($method)), #change this literal will disconfigure in fundraising/actions/contribute.php
		));
	}
}
echo $buttons;
echo $forms;	
?>
</div>


