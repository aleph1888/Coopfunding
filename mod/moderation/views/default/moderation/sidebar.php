<?php

if (elgg_is_admin_logged_in() && elgg_is_active_plugin('projects-contact')) {
        $contact_link = elgg_view('output/url', array(
                'href' => "projects_contact/add/{$params['entity']->alias}",
                'text' => elgg_echo('projects_contact:add'),
                'is_trusted' => true,
        ));
}

$body = $contact_link . "<br />";

$revisions = get_moderation_revisions();
foreach ($revisions as $revision) {
	$body .= "<b>" . $revision->guid . "</b>". "<br />";
	$body .= "created: " .elgg_get_friendly_time($revision->time_created) . "<br />";
	$body .= "modified: " .elgg_get_friendly_time($revision->time_updated	) . "<br />";	
}

echo elgg_view_module('aside', elgg_echo('revisions'), $body);
