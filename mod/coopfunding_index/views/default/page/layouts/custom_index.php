<?php
/**
 * Elgg custom index layout
 * 
 * You can edit the layout of this page with your own layout and style. 
 * Whatever you put in this view will appear on the front page of your site.
 * 
 */

$mod_params = array('class' => 'elgg-module-highlight');

?>

<div class="custom-index elgg-main elgg-grid clearfix">
	<div class="elgg-col elgg-col-1of1">
		<div class="elgg-inner pvm prl">
<?php

if ($vars['projects_categories']) {
	echo elgg_view_module('info',  elgg_echo("custom:projects:categories"), $vars['projects_categories'], $mod_params);
}
if ($vars['projects_featured']) {
	echo elgg_view_module('info',  elgg_echo("custom:projects:featured"), $vars['projects_featured'], $mod_params);
}
if ($vars['projects_tagcloud']) {
	echo elgg_view_module('info',  elgg_echo("custom:projects:tagcloud"), $vars['projects_tagcloud'], $mod_params);
}
if ($vars['projects_latest']) {
	echo elgg_view_module('info',  elgg_echo("custom:projects:latest"), $vars['projects_latest'], $mod_params);
}

?>
		</div>
	</div>
</div>
