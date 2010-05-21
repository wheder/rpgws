<?php

/**
 * @author gambler
 * @version 1.0
 * @created 21-V-2010 12:43:59
 */
class Authentizator
{

    private $m_DB;

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
        $query = "SELECT mr.modules_right_id, mr.module_id FROM " . $rpgws_config['db']['prefix'] . "modules_rights AS mr";
        $query .= " JOIN " . $rpgws_config['db']['prefix'] . "modules AS m USING module_id";
        $query .= " WHERE m.name = " . $this->m_DB->quote($module) . ")";
        $query .= " AND mr.name = " . $this->m_DB->quote($module_right);
        
        $result = $this->m_DB->query($query);
        
        if($this->m_DB->num_rows() > 1)
        {
            throw new NonExistRightException("Modul $module pozadoval overeni prava $module_right, ktere neexistuje", "Neexistujici prava.", "Modul pozadoval neexistujici prava", 5001);
        }
           
        $right_id = $result[0]['modules_right_id'];
        $module_id = $result[0]['module_id'];
        
        //nacteni grup uzivatele pro dany modul
        $query = "SELECT DISTINCT ug.group_id FROM " . $rpgws_config['db']['prefix'] . "user_group AS ug";
        $query .= " JOIN " . $rpgws_config['db']['prefix'] . "groups AS groups USING group_id";
        $query .= " WHERE groups.module_id = " . $this->m_DB->quote($module_id);
        $query .= " AND ug.user_id = " . $this->m_DB->quote($user);
        
        $result = $this->m_DB->query($query);
        if($this->m_DB->num_rows() < 1)
        {
            return false;
        }
        foreach($result as $key => $value)
        {
            $result[$key] = $value['group_id'];
        }
        
        $groups = implode(", ", $result);
        
        //kontrola prav
        $query = "SELECT value FROM " . $rpgws_config['db']['prefix'] . "rights";
        $query .= " WHERE group_id IN ($groups)";
        $query .= " ORDER BY value LIMIT 1";

        $result = $this->m_DB->query($query);
        if($this->m_DB->num_rows() < 1)
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