<?php

class Db_MySQLPDO implements Db_Abstract {
    private $connetion;
    private $aff_rows;
    private $num_rows;
    
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
        
        $this->aff_rows = 0;
        $this->num_rows = 0;
    }

    public function affected_rows()
    {
        return $this->aff_rows;
    }

    public function last_insert_id()
    {
        return $this->connection->lastInsertId();
    }

    public function num_rows()
    {
        return $this->num_rows;
    }

    /**
     * Metoda provede dotaz na databazy 
     * @param string
     * @return array     
     */
    public function query($sql)
    {
        $this->num_rows = 0;
        $this->aff_rows = 0; 
        if(!$result = $this->connection->query($sql)) 
        {
            $msg = "Database query: '$sql' failed with error ";
            $msg .= $this->connection->errorCode() . ":\n";
            $errInfo =$this->connection->errorInfo(); 
            $msg .=  $errInfo[2];
            throw new DbQueryException($msg, "DB Query failed", "DB Query failed", 1001);
        }
        
        $ret = 0;
        if(preg_match("/SELECT/", $sql))
        {
            $ret = $result->fetchAll();
            $this->num_rows = count($ret); 
        }
        else
        {
            $this->aff_rows = $result->rowCount();
        }
        return $ret;
    }

    /**
     * Metoda pro obaleni retezce uvozovkami
     * 
     * @param string
     * @return string
     */
    public function quote($str)
    {
        return $this->connection->quote($str);
    }
    
}


?>
