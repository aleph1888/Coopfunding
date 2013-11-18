<?php

//Global site BAN
if (!elgg_get_config('ban')) {
	register_error(elgg_echo('Configure system BAN in settings.php.'));
}

return true;
