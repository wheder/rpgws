<?php

function __autoload($classname) {
    
    if (preg_match("/^[a-zA-Z]*Exception$/i", $classname) && strpos("_", $classname) === false) {
        eval("class $classname extends Exceptions{}");
        return true;
    }
    
    $filename = strtolower($classname).'.php';
    $path = RPGWS_CORE_PATH;
    while (strpos($filename, "_") !== false) {
        $exploded = explode("_", $filename, 2);
        $path .= "/". $exploded[0];
        $filename = $exploded[1];
    }
    if (file_exists($path."/".$filename)) {
        require $path."/".$filename;
        return true;
    }
    $filename = strtolower($classname).'.php';
    $path = RPGWS_MODULES_PATH;
    while (strpos($filename, "_") !== false) {
        $exploded = explode("_", $filename, 2);
        $path .= "/". $exploded[0];
        $filename = $exploded[1];
    }
    if (file_exists($path."/".$filename)) {
        require $path."/".$filename;
        return true;
    }

    eval("class $classname {}");
    throw new Exception("Class $classname not found!");
}
?>
