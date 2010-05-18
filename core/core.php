<?php


class Core {
    
    private $m_Request;
    
    public function run() {
        session_start();
        phpinfo(INFO_VARIABLES);
        var_dump($_REQUEST);
        
        
        //zakladni osetreni vyjimek
        try
        {
            $this->m_Request = new Request();
            $this->m_Request->process();
        }
        catch (Exception $ex)
        {
            echo "<h3>Exception: " . $ex->getMessage() . "</h3>";
            echo $ex->getTraceAsString();
        }
        
    }


}
