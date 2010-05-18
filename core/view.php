<?php
/**
 * @author gambler
 * @version 1.0
 * @created 18-V-2010 16:06:11
 */
class View
{

    private $m_Menu;
    private $properties;
    private $content_script;
    private $layout_script;
    
    
    public function __get($name) {
        if(isset($properties[$name])) return $properties[$name];
        else return "";
    }
    
    public function __set($name, $value) {
        $properties[$name] = $value;
    }
    
    public function __construct()
    {   
    
    }
    
    /**
     * Metoda nastavi, ktery soubor se ma pouzit pro vytvoreni obsahu
     * 
     * @param string
     * @return void
     */                        
    public function set_conent($script)
    {
        $this->content_script = $script;
    }
    
    /**
     * Metoda nastavi, ktery soubor se ma pouzit pro vytvoreni layoutu
     * 
     * @param string
     * @return void
     */                        
    public function set_layout($script)
    {
        $this->layout_script = $script;
    }
    /**
     * metoda vytvori obsah
     */         
    public function get_content($content_script)
    {
        ob_start();
        include $content_script;
        
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }

    /**
     * Metoda vytvori stranku a preda ji k dalsimu pouziti
     * @param content
     */
    public function get_layout($content, $layout_script)
    {
        //$menu = $this->m_Menu->get_menu()
        ob_start();
        include $layout_script;
        
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result; 
    }

    public function print()
    {
        $content = $this->get_content($this->content_script);
        echo $this->get_layout($content, $this->layout_script);
    }

}
?>