<?php

//User for anonymous contributions
if (!get_user_by_username("Someone")) {
	$guid = register_user ("Someone", generate_random_cleartext_password(), "Someone", "Someone@mail.mail", false);
	elgg_set_user_validation_status ($guid, "true", "by_admin");	
}
