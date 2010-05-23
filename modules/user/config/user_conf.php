<?php

$user_config = array();
$user_config['nick']['maxlength'] = 30;
$user_config['nick']['minlength'] = 3;
$user_config['nick']['regexp']['match_required'] = true;
$user_config['nick']['regexp']['content'] = '/^[a-zA-Z0-9]*$/';

$user_config['password']['minlength'] = 4;
$user_config['password']['maxlength'] = 30;
$user_config['password']['generated_length'] = 8;
$user_config['password']['regexp']['match_required'] = false;
$user_config['password']['regexp']['content'] = '';

$user_config['mail']['minlength'] = 4;
$user_config['mail']['maxlength'] = 50;
$user_config['mail']['regexp']['match_required'] = true;
$user_config['mail']['regexp']['content'] = '/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/';

$user_config['mailer']['from'] = "no-reply@wheder.info";
$user_config['mailer']['reply'] = "no-reply@wheder.info";
$user_config['mailer']['new_reg_subject'] = "Registrace na wheder.info";
?>
