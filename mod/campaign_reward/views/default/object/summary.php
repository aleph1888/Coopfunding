<?php

$entity = $vars['entity'];

if ($entity)
{
	$title = $entity->title;
	$subtitle = '';
	$content = elgg_extract('content', $vars, '');
	$metadata = elgg_extract('metadata', $vars, '');

	echo "<h4>$title</h4>";

	echo $metadata;

	echo "<div class=\"elgg-subtext\">$subtitle</div>";

	if ($content) {
		echo "<div class=\"elgg-content\">$content</div>";
	}
}

