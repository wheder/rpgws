<?php

$rpgws_config = Array();
$rpgws_config['db'] = Array();
$rpgws_config['db']['adapter'] = 'mysqlpdo'; //required
$rpgws_config['db']['host'] = 'localhost';
$rpgws_config['db']['name'] = 'rpgws';
$rpgws_config['db']['user'] = 'rpgws';
$rpgws_config['db']['pass'] = 'secret password';
$rpgws_config['db']['port'] = '3306';
$rpgws_config['db']['prefix'] = 'RPGWS_';

//better keep it default
$rpgws_config['logs']['level'] = 2;

$rpgws_config['layout']['default'] = 'default.php';
$rpgws_config['view']['error'] = 'error.php';
$rpgws_config['view']['welcome'] = 'welcome.php';

$rpgws_config['flood_prot']['time_limit'] = 5*60; //limit in seconds
$rpgws_config['flood_prot']['limit'] = 5; //amount of allowed attempts
//$rpgws_config['db']['socket'] = '';


?>
