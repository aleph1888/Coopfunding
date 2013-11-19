<?php

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('projects:notfound');
	return true;
}

$project = $vars['entity'];
$owner = $project->getOwnerEntity();
$categories = elgg_view('output/categories', $vars);

if (!$owner) {
	// not having an owner is very bad so we throw an exception
	$msg = elgg_echo('InvalidParameterException:IdNotExistForGUID', array('project owner', $project->guid));
	throw new InvalidParameterException($msg);
}
$title = elgg_view('output/url', array('text' => $project->name, 'href' => $project->getURL()));
$tags = elgg_view('output/tags', array('value' => $project->interests));
?>

<div class="projects-gallery-item">
	<p class="projects-gallery-photo"><?php echo elgg_view_entity_icon($project, 'medium') ?></p>
	<h3><?php echo $title ?></h3>
	<div class="projects-gallery-categories" ><?php echo $categories; ?></div>
	<div class="projects-gallery-tags" ><?php echo $tags; ?></div>
	<div class="projects-gallery-info">
		<p class="projects-gallery-subtitle"><?php echo $project->briefdescription?></p>		
	</div>
	
</div>
