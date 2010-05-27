<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:43:03
 */
class DrD_Quest_Model
{

    private $active;
    private $description;
    private $game_master_id;
    private $characters;
    private $quest_id;
    private static $m_DB = null;

    function __construct()
    {
        $this->active = false;
        $this->description = "";
        $this->game_master_id = 0;
        $this->characters = array();
        if(self::$m_DB === null) self::$m_DB = Db::get(); 
    }

    function __destruct()
    {
    }



    
    /**
     * Obecny getter pro vlastnosti
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(empty($name))
        {
            return null;
        }
        
        if($name == "active") $method = "isactive";
            else $method = "get" . $name;
            
        if(method_exists($this,$method)) 
        {
            return $this->$method();
        } else {
            return null;
        }
    }

    /**
     * Obecny setter pro vlastnosti
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if(empty($name)) {
            return;
        }
        $method = "set" . $name;
        if(method_exists($this,$method)) 
        {
            $this->$method($value);
        }
    }
    
    /**
     * Magicka metoda pro zjisteni existence vlastnosti
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $method = "get" . $name;
        if(!method_exists($this,$method)) return false;
        
        $var = $this->$method();
        return (!empty($var));
    }

    /**
     * Prida postavu do questu
     * 
     * @param int $char
     * @return void
     */
    public function add_character($char)
    {
        if($char < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze pridat postavu do questu.", "Neplatné id postavy", "Neplatné id postavy.", 6211);
        $character = DrD_Character_Model::load($char);
        $character->add_to_quest($this->quest_id);
        $character->save();
        array_push($this->characters, $char);
    }

    public function getdescription()
    {
        return $this->description;
    }

    public function getgame_master_id()
    {
        return $this->game_master_id;
    }

    public function getquest_id()
    {
        return $this->quest_id;
    }
    
    public function getcharacters()
    {
        return $this->characters;
    }

    /**
     * Zjisti zda se postava ucastni questu
     * 
     * @param int $char
     * @return bool
     */
    public function is_character($char)
    {
        if($char < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze zjistit zda je postava v questu.", "Neplatné id postavy", "Neplatné id postavy.", 6212);
        return (in_array($char, $this->characters)); 
    }

    public function isactive()
    {
        return $this->active;
    }

    /**
     * Nacte quest podle jeho id
     * @param int $id
     */
    public static function load($id)
    {
        if($id < 1) throw new UnexpectedQuestIdException("Neočekávané id questu -- nelze načíst quest.", "Neplatné id questu", "Nelze načíst quest.", 6213);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        global $rpgws_config;
        $query = "
            SELECT
                *,
                CAST(active AS UNSIGNED) AS active
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
            WHERE
                drd_quest_id = " . self::$m_DB->quote($id) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) throw new NotExistsQuestException("Quest s id $id neexistuje", "Quest neexistuje", "Požadovaný quest neexistuje.", 6214);
        $result = $result[0];
        
        $quest = new self();
        $quest->active = ($result['active'] == 1);
        $quest->description = $result['description'];
        $quest->game_master_id = $result['game_master_id'];
        $quest->quest_id = $result['drd_quest_id'];
        $quest->load_characters();
        
        return $quest;
    }
    
    /**
     * Metoda pro nacteni postav v questu
     * 
     * @access protected
     * @return void
     */
    protected function load_characters()
    {
        if($this->quest_id < 1) throw new UnexpectedQuestIdException("Neočekávané id questu -- nelze načíst postavy v questu.", "Neplatné id questu", "Nelze načíst postavy v questu.", 6215);
        
        global $rpgws_config;
        $this->characters = array();
        
        $query = "
            SELECT
                drd_character_id
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quest_members
            WHERE
                drd_quest_id = " . self::$m_DB->quote($this->quest_id) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) return;
        
        foreach($result as $row)
        {
            array_push($this->characters, $row['drd_character_id']);
        }
        
    }
    
    /**
     * Metoda nacte vsechny questy v DB
     * 
     * @return array
     */
    public static function load_all()
    {
        global $rpgws_config;
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        $query = "
            SELECT
                *,
                CAST(active AS UNSIGNED) AS active
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
        ";
        
        $result = self::$m_DB->query($query);
        $ret = array();
        
        if(self::$m_DB->num_rows() < 1) return $ret;
        
        foreach($result as $row)
        {
            $quest = new self();
            $quest->active = ($row['active'] == 1);
            $quest->description = $row['description'];
            $quest->game_master_id = $row['game_master_id'];
            $quest->quest_id = $row['drd_quest_id'];
            $quest->load_characters();
            $ret[$quest->quest_id] = $quest;
        }

        return $ret;
    }
    
    /**
     * Metoda nacte vsechny aktivni questy v DB
     * @return void
     */
    public static function load_all_active()
    {
        global $rpgws_config;
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        $query = "
            SELECT
                *,
                CAST(active AS UNSIGNED) AS active
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
            WHERE 
                active = b'1'
        ";
        
        $result = self::$m_DB->query($query);
        $ret = array();
        
        if(self::$m_DB->num_rows() < 1) return $ret;
        
        foreach($result as $row)
        {
            $quest = new self();
            $quest->active = ($row['active'] == 1);
            $quest->description = $row['description'];
            $quest->game_master_id = $row['game_master_id'];
            $quest->quest_id = $row['drd_quest_id'];
            $quest->load_characters();
            $ret[$quest->quest_id] = $quest;
        }

        return $ret;
    }
    
    /**
     * Nacte questy dane postavy
     * 
     * @param int $char
     * @return array
     */
    public static function load_by_char($char)
    {
        if($char < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze načíst questy postavy.", "Neplatné id postavy", "Neplatné id postavy.", 6218);
        global $rpgws_config;
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        $query = "
            SELECT
                *,
                CAST(active AS UNSIGNED) AS active
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
            WHERE
               drd_quest_id IN (
                   SELECT
                       drd_quest_id
                   FROM
                       " . $rpgws_config['db']['prefix'] . "drd_quest_members
                   WHERE
                       drd_character_id = " . self::$m_DB->quote($char) .")
        ";
        
        $result = self::$m_DB->query($query);
        $ret = array();
        
        if(self::$m_DB->num_rows() < 1) return $ret;
        
        foreach($result as $row)
        {
            $quest = new self();
            $quest->active = ($row['active'] == 1);
            $quest->description = $row['description'];
            $quest->game_master_id = $row['game_master_id'];
            $quest->quest_id = $row['drd_quest_id'];
            $quest->load_characters();
            $ret[$quest->quest_id] = $quest;
        }

        return $ret;
    }

    /**
     * Metoda odstrani postavu z questu 
     *
     * @param int $char
     * @return void
     */
    public function rem_character($char)
    {
        if($char < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze odstranit postavu z questu.", "Neplatné id postavy", "Neplatné id postavy.", 6216);
        
        $idx = array_search($char, $this->characters);
        
        if($idx === false) return;
        
        $character = DrD_Character_Model::load($char);
        $character->rem_from_quest($this->quest_id);
        $character->save();
        
        unset($this->characters[$idx]);
    }

    /**
     * Pomocna metoda pro update questu v DB
     * 
     * @return void
     */
    protected function update()
    {
        if($this->quest_id < 1) UnexpectedQuestIdException("Neočekávané id questu -- nelze updatovat quest.", "Neplatné id questu", "Nelze uložit quest.", 6217);
        
        global $rpgws_config;
        
        $active = ($this->active ? "b'1'" : "b'0'");
        $query = "
            UPDATE
                " .  $rpgws_config['db']['prefix'] . "drd_quests
            SET
                active = " . $active . ",
                description = " . self::$m_DB->quote($this->description) . ",
                game_master_id = " . self::$m_DB->quote($this->game_master_id) . "
            WHERE
                drd_quest_id = " . self::$m_DB->quote($this->quest_id) . "
        ";
        
        self::$m_DB->query($query);
    }
    
    /**
     * Pomocna metoda pro vlozeni questu do DB
     * 
     * @return void
     */
    protected function insert()
    {
        global $rpgws_config;
        
        $active = ($this->active ? "b'1'" : "b'0'");
        $query = "
            INSERT INTO
                " .  $rpgws_config['db']['prefix'] . "drd_quests
                (active, description, game_master_id)
            VALUES (
                " . $active . ",
                " . self::$m_DB->quote($this->description) . ",
                " . self::$m_DB->quote($this->game_master_id) . ")
        ";
        
        self::$m_DB->query($query);
        $this->quest_id = self::$m_DB->last_insert_id();
    }
    
    /**
     * Metoda pro ulozeni do databaze
     * @return void
     */
    public function save()
    {
        if($this->quest_id > 0) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * 
     * @param newVal
     */
    public function setactive($newVal)
    {
        $this->active = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setdescription($newVal)
    {
        $this->description = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setgame_master_id($newVal)
    {
        $this->game_master_id = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setquest_id($newVal)
    {
        $this->quest_id = $newVal;
    }

}
?>
