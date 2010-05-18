<?php


class Core {
    
    private $m_Request;
    
    public function run() {
        session_start();
        phpinfo(INFO_VARIABLES);
        var_dump($_REQUEST);
        
        
        $m_Request = new Request();
        $m_Request->process();
        
    }
    
    



}



