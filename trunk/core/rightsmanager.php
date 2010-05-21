<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 21-V-2010 15:42:00
 */
class RightsManager
{

    private $modul_id;
    private $m_DB;

    function __construct()
    {
        $this->modul_id = -1;
        $this->m_DB = DB::get();
    }

    /**
     * Nacte id modulu
     * @param string $module
     * @return int
     */
    private function get_module_id($module)
    {
        if($this->modul_id < 0)
        {
            global $rpgws_config;
            $query = "
            	SELECT
            	     module_id
                FROM 
                    " . $rpgws_config['db']['prefix'] . "modules
                WHERE
                    name = " . $this->m_DB->quote($module) . "
            ";
        
            $result = $this->m_DB->query($query);
        
            if($this->m_DB->num_rows() < 1)
            {
                throw new NotInstaledModulException("Nepodarilo se nacist id modulu $module, pravdepodobne nebyl instalovan", "Neexistujici modul.", "Modul pravdepodobne nebyl korektne instalovan", 6001);
            }
        
            $this->modul_id = $result[0]['module_id'];
        }
        
        return $this->modul_id;
    }

    /**
     * Vytvoří skupinu předaného jména 
     *
     * @param string $group_name
     * @param string $description
     * @return void
     */
    public function create_group($group_name, $description)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module);
        
        $gid = 0;
        if($this->exist_group($group_name, $gid))
        {
            throw new NewGroupExistsException("Modul $module se pokousel vytvorit skupinu, ktera jiz existuje.", "Skupina existuje", "Skupina $group_name jiz v modulu existuje", 5011);
        }
        
        $query = "
            INSERT INTO 
                " . $rpgws_config['db']['prefix'] . "groups
                (module_id, name, description)
             VALUES(
                 " . $this->m_DB->quote($module_id) . ",
                 " . $this->m_DB->quote($group_name) . ",
                 " . $this->m_DB->quote($description) . ")
        ";
        
        $this->m_DB->query($query);
    }

    /**
     * Vytvoří právo
     * @param string $right
     * @return void
     */
    public function create_right($right)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module); 

        $rid = 0;
        if($this->exist_right($right, $rid))
        {
            throw new NewRightExistsException("Modul $module se pokousel vytvorit pravo, ktere jiz existuje.", "Právo již existuje", "Právo $right již v modulu existuje", 5012);
        }
        
        $query = "
        	INSERT INTO
        	    " . $rpgws_config['db']['prefix'] . "modules_rights
        	    (module_id, name) 
        	VALUES(
        	    " . $this->m_DB->quote($module_id) .",
        	    " . $this->m_DB->quote($right) . ")
        ";
        
        $this->m_DB->query($query);
    }

    /**
     * Odstraní skupinu z databáze
     * @param string $group_name
     * @return void
     */
    public function delete_group($group_name)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module);
        
        $gid = 0;
        if(!$this->exist_group($group_name, $gid))
        {
            throw new GroupDoesntExistsException("Modul $module se pokousel smazat skupinu, ktera neexistuje.", "Skupina neexistuje", "Skupina $group_name jiz v modulu neexistuje", 5013);
        }
        
        $query = "
            DELETE FROM
                " . $rpgws_config['db']['prefix'] . "groups
            WHERE
                group_id = " . $this->m_DB->quote($gid) . "
        ";
        
        $this->m_DB->query($query);
    }

    /**
     * Odstrani pravo z databaze
     * @param string $right
     * @return void
     */
    public function delete_right($right)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module); 

        $rid = 0;
        if(!$this->exist_right($right, $rid))
        {
            throw new RightDoesntExistsException("Modul $module se pokousel smazat pravo, ktere neexistuje.", "Právo neexistuje", "Právo $right nemůže být smazáno protož neexistuje", 5014);
        }
        
        $query = "
            DELETE FROM
                " . $rpgws_config['db']['prefix'] . "modules_rights
            WHERE 
                modules_right_id = " . $this->m_DB->quote($rid) . "
        ";
        
        $this->m_DB->query($query);
    }

    /**
     * Zjistí zda skupina existuje
     * Do druheho parametru bude ulozeno id skupiny
     *  
     * @param string $group_name
     * @param int &$gid 
     * @return bool
     */
    public function exist_group($group_name, &$gid)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $mod_id = $this->get_module_id();
        
        $query = "
            SELECT
                group_id
            FROM 
                " . $rpgws_config['db']['prefix'] . "groups
            WHERE
                name = " . $this->m_DB->quote($group_name) ."
                AND module_id = " . $this->m_DB->quote($mod_id) . "
        ";
        
        $result = $this->m_DB->query($query);
        
        if($this->m_DB->num_rows() > 0)
        {
            $gid = $result[0]['group_id'];
            return true;
        } else return false;
    }

    /**
     * Zjistí zda práva existují
     * Do druheho parametru bude ulozeno id prava
     * @param right
     */
    public function exist_right(string $right, &$rid)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $mod_id = $this->get_module_id();
        
        $query = "
            SELECT
                modules_right_id
            FROM 
                " . $rpgws_config['db']['prefix'] . "modules_rights
            WHERE 
                name = " . $this->m_DB->quote($group_name) . "
                AND module_id = " . $this->m_DB->quote($mod_id) . "
        ";
        
        $result = $this->m_DB->query($query);
        
        if($this->m_DB->num_rows() > 0)
        {
            $rid = $result[0]['modules_right_id'];
            return true;
        } else return false;
    }

    /**
     * Odstraní právo dané skupině
     * @param group_name
     * @param right
     */
    public function remove_right(string $group_name, string $right)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module); 
        
        $rid = 0;
        $gid = 0;
        if(!$this->exist_right($right, $rid))
        {
            throw new RightDoesntExistsException("Modul $module se pokousel smazat pravo, ktere neexistuje.", "Právo neexistuje", "Právo $right nemůže být smazáno protož neexistuje", 5014);
        }
        
        if(!$this->exist_group($group_name, $gid))
        {
            throw new GroupDoesntExistsException("Modul $module se pokousel smazat skupinu, ktera neexistuje.", "Skupina neexistuje", "Skupina $group_name jiz v modulu neexistuje", 5013);
        }
        
        $query = "
            DELETE FROM
                " . $rpgws_config['db']['prefix'] . "rights
            WHERE
                group_id = " . $this->m_DB->quote($gid) . "
                AND module_right = " . $this->m_DB->quote($rid) . "
        ";
        
        $this->m_DB->query($query);
    }

    /**
     * Nastaví práva uživateli 
     * @param string $group_name
     * @param string $right
     * @param int $value
     */
    public function set_right($group_name, $right, $value)
    {
        if($value > 1) $value = 1;
        if($value < 0) $value = 0;
        
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module); 
        
        $rid = 0;
        $gid = 0;
        if(!$this->exist_right($right, $rid))
        {
            throw new RightDoesntExistsException("Modul $module se pokousel smazat pravo, ktere neexistuje.", "Právo neexistuje", "Právo $right nemůže být smazáno protož neexistuje", 5014);
        }
        
        if(!$this->exist_group($group_name, $gid))
        {
            throw new GroupDoesntExistsException("Modul $module se pokousel smazat skupinu, ktera neexistuje.", "Skupina neexistuje", "Skupina $group_name jiz v modulu neexistuje", 5013);
        }
        
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "
                rights(module_right, group_id, value)
            VALUES(
                " . $this->m_DB->quote($rid) .",
                " . $this->m_DB->quote($gid) .",
                " . $this->m_DB->quote($value) . ")
            ON DUPLICATE KEY UPDATE 
                    value = " . $this->m_DB->quote($value) ."
        ";
        
        $this->m_DB->query($query);
    }
    
    /**
     * Přidá uživatele do skupiny
     * 
     * @param string $group
     * @param int $user_id
     * @return void
     */
    public function add_user($group, $user_id)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module);

        $gid = 0;
        if(!$this->exist_group($group_name, $gid))
        {
            throw new GroupDoesntExistsException("Modul $module se pokousel smazat skupinu, ktera neexistuje.", "Skupina neexistuje", "Skupina $group_name jiz v modulu neexistuje", 5013);
        }
        
        $query = "
            INSERT IGNORE
                " . $rpgws_config['db']['prefix'] . "user_group
                (user_id, group_id)
            VALUES(
                " . $this->m_DB->quote($user_id) . ",
                " . $this->m_DB->quote($gid) . ")
        ";
        
        $this->m_DB->query($query);
    }
    
    /**
     * Odebere uživatele ze skupiny
     * 
     * @param string $group
     * @param int $user_id
     * @return void
     */
    public function rem_user($group, $user_id)
    {
        global $rpgws_config;
        $req = Request::getInstance();
        $module = $req->get_module();
        $module_id = $this->get_module_id($module);

        $gid = 0;
        if(!$this->exist_group($group_name, $gid))
        {
            throw new GroupDoesntExistsException("Modul $module se pokousel smazat skupinu, ktera neexistuje.", "Skupina neexistuje", "Skupina $group_name jiz v modulu neexistuje", 5013);
        }
        
        $query = "
        	DELETE FROM 
        	    " . $rpgws_config['db']['prefix'] . "user_group
        	WHERE
        	    user_id = " . $this->m_DB->quote($user_id) . "
                AND group_id=" . $this->m_DB->quote($gid) . "
        ";
        
        $this->m_DB->query($query);   
    }
}
?>