<?php

class Db_MySQLPDO implements Db_Abstract {
    private $connetion;
    function __construct() {
        $db_info = &$GLOBALS['rpgws_config']['db'];
        $required = Array('host', 'name', 'user', 'pass');
        foreach ($required as $val) {
            if (!isset($db_info[$val])) throw new Exception('Fatal database error. Value "'.$val.'" must be set up!');
        }
        $dsn = 'mysql:host='.$db_info['host'].';dbname='.$db_info['name'];
        if (isset($db_info['port'])) $dsn .= ';port='.$db_info['port'];
        
        try {
            $this->connection = new PDO($dsn, $db_info['user'], $db_info['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit (1);
        }
        
        
        
    }


}


?>