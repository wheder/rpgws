<?php

if (file_exists(RPGWS_CONFIG . "/local.php")) {
    include RPGWS_CONFIG . "/local.php";
}
else {
    require RPGWS_CONFIG . "/setup.php";
    exit( 1 );
}

defined('RPGWS_CORE_PATH')
    || define('RPGWS_CORE_PATH', realpath(dirname(__FILE__) . '/../core'));

defined('RPGWS_MODULES_PATH')
    || define('RPGWS_MODULES_PATH', realpath(dirname(__FILE__) . '/../modules'));

defined('RPGWS_ENVINRONMENT')
    || define('RPGWS_ENVINRONMENT', 'public');

defined('RPGWS_LAYOUT_PATH')
    || define('RPGWS_LAYOUT_PATH', realpath(dirname(__FILE__) . '/../layout'));
    
require RPGWS_CONFIG . '/autoload.php';


$core = new Core();
$core->run();
