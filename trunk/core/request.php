<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 18-V-2010 12:54:27
 */
class Request
{

    private $getted_vars;
    private $posted_vars;
    private $m_Dispatcher;
    private $module;
    private $controller;
    private $action;
    
    /**
     * Konstruktor tøídy naète hodnoty get a post do polí
     */          
    public function __construct()
    {
        $this->posted_vars = $_POST;
    }



    /**
     * Metoda ziska z poslanych hodnot double hodnotu podle predaneho jmena.
     *      
     * @param name
     * @throws Exception
     * @return double          
     */
    public function get_double($name)
    {
        $this->check_if_set();
        
        $result = 0.0;
        if(isset($getted_vars[$name])) $result = $getted_vars[$name];
       
        if(isset($posted_vars[$name])) $result = $posted_vars[$name];
       
        if(!is_numeric($result)) throw new Exception("Variable $name isn't a number.");
       
        return $result;
    }

    /**
     * Metoda ziska z poslanych hodnot integer hodnotu podle predaneho jmena.
     *      
     * @param name
     * @throws Exception
     * @return double
     */
    public function get_integer($name)
    {
        $result = $this->get_double($name);
        $result = floor($result);
       
        return $result;
    }

    /**
     * Metoda ziska z poslanych hodnot retezec podle predaneho jmena.
     *          
     * @param name
     * @param maxlength
     */
    public function get_string($name, $maxlength)
    {
        $result = "";
        $this->check_if_set($name);
        
        if(isset($getted_vars[$name])) $result = $getted_vars[$name];
       
        if(isset($posted_vars[$name])) $result = $posted_vars[$name];
       
        if(!is_string($result)) throw new Exception("Variable $name isn't a string.");
        
        if(sizeof($result) > $maxlength) throw new Exception("String in variable $name is too long.");        
    }

    /**
     * metoda zpracuje retezec v URL
     */     
    private function parse_get()
    {
        if(isset($_SERVER['REDIRECT_URL'])) 
        {
            $get = $_SERVER['REDIRECT_URL'];
            $get = explode('/', $get);
            $this->module = $get[0];
            $this->controller = (isset($get[1]) ? $get[1] : "");
            $this->action = (isset($get[2] ? $get[2] : "");
            
            $i = 3;
            while($i < count($get)) {
                $this->getted_vars[$i - 3] = $get[$i]; 
            }
        }
    }

    public function process()
    {
        $this->m_Dispatcher = new Dispatcher();
        $this->m_Dispatcher->dispatch($this);
    }

    /**
     * Zjistí zda daná promìná byla zaslána v po¾adavku.
     * 
     * @throws Exception     
     * @param string
     * @return bool     
     */
    public function check_if_set($name)
    {
        if(empty($name)) throw new Exception('Empty requested variable name.'); 
        if(!isset($getted_vars[$name]) && !isset($posted_vars[$name])) throw new Exception("Non-existing requested variable $name.");
        
        return true;
    }

    /**
     * Metoda vrátí akci po¾adovanou requestem
     * 
     * @return string
     */                   
    public function get_action()
    {  
       return $this->action;
    }

    /**
     * Metoda vrátí controller po¾adovaný requestem
     * 
     * @return string
     */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
     * Metoda vrátí modul po¾adovaný requestem
     * 
     * @return string
     */
    public function get_module()
    {
        return $this->module;
    }
}
?>