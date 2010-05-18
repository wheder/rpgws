<?php

class DB {
    private static $instance;
    
    public static function get() {
        if (self::$instance === NULL ) {
            $class_name = "Db_".$GLOBALS['rpgws_config']['db']['adapter'];
            self::$instance = new $class_name();
            if (!(self::$instance instanceof Db_Abstract)) throw new Exception('Application logic fatal error. "'.self::$instance.'" does not implement Db_Abstract.');
        }
        return self::$instance;
    }
}
?>