<?php

/**
 * @author gambler
 * @version 1.0
 * @created 21-V-2010 12:43:59
 */
class Authentizator
{

    public $m_DB;

    function __construct()
    {
        $this->m_DB = Db::get();
    }

    function __destruct()
    {
    }



    /**
     * Overi zda uzivatel ma pozadovane pravo
     * 
     * @param int $user id uzivatele
     * @param string $module_right pozadovane pravo
     * @return bool
     */
    public function has_right($user, $module_right)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        
        //kontrola jestli pozadovane pravo existuje
        $query = "SELECT modules_right_id FROM " . $rpgws_config['db']['prefix'] . "modules_rights ";
        $query .= " WHERE module_id = (SELECT module_id FROM " . $rpgws_config['db']['prefix'];
        $query .= " WHERE name = " . $this->m_DB->quote($module) . ") AND name = ";
        $query .= $this->m_DB->quote($module_right);
        
        $result = $this->m_DB->query($query);
        
        if($this->m_DB->num_rows() > 1)
        {
            throw new NonExistRightException("Modul $module pozadoval overeni prava $module_right, ktere neexistuje", "Neexistujici prava.", "Modul pozadoval neexistujici prava", 5001);
        }
           
        $right_id = $result[0]['modules_right_id'];
        
        //kontrola prav
        $query = "SELECT value FROM " . $rpgws_config['db']['prefix'] . "rights";
        $query .= " WHERE group_id IN (SELECT group_id FROM " . $rpgws_config['db']['prefix'] . "user_group";
        $query .= " WHERE user_id = " . $this->m_DB->quote($user) . ")";
        $query .= " ORDER BY value LIMIT 1";

        $result = $this->m_DB->query($query);
        if($this->m_DB->num_rows() > 1)
        {
            return false;
        }
        
        if($result['value'] = 1)
        {
            return true;    
        }
        else
        {
            return false;
        }
    }

}
?>