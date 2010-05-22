<?php

global $user_config;

$user_config = array();
$user_config['nick']['maxlength'] = 30;
$user_config['nick']['minlength'] = 3;
$user_config['nick']['regexp']['match_required'] = true;
$user_config['nick']['regexp']['content'] = '/[a-zA-Z0-9]*/';

$user_config['password']['minlength'] = 4;
$user_config['password']['maxlength'] = 30;
$user_config['password']['regexp']['match_required'] = false;
$user_config['password']['regexp']['content'] = '';

$user_config['mail']['minlength'] = 4;
$user_config['mail']['maxlength'] = 30;
$user_config['mail']['regexp']['match_required'] = true;
$user_config['mail']['regexp']['content'] = '/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/';

?>
