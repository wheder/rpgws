<?php

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 18-V-2010 12:54:27
 */
class Request
{

    private $full_uri = Array();
    private $old_uri = Array();
    private $new_uri = Array();
    private $getted_vars;
    private $posted_vars;
    private $m_Dispatcher;
    private $module;
    static private $instance;
    
    /**
     * Konstruktor tridy nacte hodnoty get a post do poli
     */          
    public function __construct()
    {
        $this->posted_vars = $_POST;
        $this->parse_get();
        self::$instance = $this;
    }

    /**
     * Ziska instanci requestu
     * 
     * @return Request
     */
    public static function getInstance() {
        if(self::$instance === null)
        {
            new self();
        }
        
        return self::$instance;
    }
    
    public function get_uri_float()
    {
        $val = $this->next_value();
        if (preg_match("/^(\-?)[0-9]+(\.?)[0-9]*$/", $val)) return $val;
        return 0.0;
    }
    public function get_uri_int()
    {
        return (int) $this->get_uri_float();
    }
    public function get_uri_id()
    {
        return abs($this->get_uri_int());
    }
    public function get_uri_string()
    {
        return $this->next_value();
    }
    
    public function get_param_float($name)
    {
        $val = $this->get_param($name);
        if (preg_match("/^(\-?)[0-9]+(\.?)[0-9]*$/", $val)) return $val;
        return 0.0;
    }
    public function get_param_int($name)
    {
        return (int) $this->get_uri_float($name);
    }
    public function get_param_id($name)
    {
        return abs($this->get_param_int($name));
    }
    public function get_param($name, $limit=null)
    {
        if (isset($this->posted_vars[$name])) $val = $this->posted_vars[$name];
        else return null;
        if ($limit !== null) {
            if (strlen($val) > $limit) return null;
        }
        return $val;
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
       
        if(isset($posted_vars[$name])) $result = $posted_vars[$name];
       
        if(!is_string($result)) throw new StringParamException("Variable $name isn't a string.");
        
        if(sizeof($result) > $maxlength) throw new StringParamException("String in variable $name is too long.");        
    }

    /**
     * metoda zpracuje retezec v URL
     */     
    private function parse_get()
    {
        if(isset($_SERVER['REDIRECT_URL'])) 
        {
            $get = $_SERVER['REDIRECT_URL'];
            $get = preg_replace("/\/+/", "/", $get);
            if (isset($get[0]) && $get[0]=='/') $get= substr($get, 1);
            
            $get = explode('/', $get);
            $this->full_uri = $get;
            $this->new_uri = $get;
            $this->module = $this->next_value();
        }
    }
    
    private function next_value() {
        $tmp = array_shift($this->new_uri);
        if ($tmp !== null) array_push($this->old_uri, $tmp);
        return $tmp;
    }
    
    public function process()
    {
        $this->m_Dispatcher = new Dispatcher();
        $this->m_Dispatcher->dispatch($this);
    }


    /**
     * Metoda vr�t� modul po�adovan� requestem
     * 
     * @return string
     */
    public function get_module()
    {
        return $this->module;
    }
}
?>