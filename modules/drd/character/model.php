<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:44:16
 */
class DrD_Character_Model
{

    private $class;
    private $description;
    private $hit_points;
    private $character_id;
    private $items;
    private $mana;
    private $name;
    private $owner;
    private $quests;
    private $race;
    private $q_changed;
    private static $m_DB = null;

    function __construct()
    {
        $this->class = null;
        $this->description = "";
        $this->hit_points = 0;
        $this->character_id = 0;
        $this->items = "";
        $this->mana = 0;
        $this->name = "";
        $this->owner = 0;
        $this->quests = array();
        $this->race = null;
        $this->q_changed = false;
        self::$m_DB = Db::get();
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
        $method = "get" . $name;
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
        return empty($var);
    }

    /**
     * Prida aktualniho uzivatele do questu
     * @param int $quest
     * @return void
     */
    public function add_to_quest($quest)
    {
        if(!in_array($quest, $this->quests)) array_push($this->quests, $quest);
        $this->q_changed = true;
    }

    public function getclass()
    {
        return $this->class;
    }

    public function getdescription()
    {
        return $this->description;
    }

    public function gethit_points()
    {
        return $this->hit_points;
    }

    public function getcharacter_id()
    {
        return $this->character_id;
    }

    public function getitems()
    {
        return $this->items;
    }

    public function getmana()
    {
        return $this->mana;
    }

    public function getname()
    {
        return $this->name;
    }

    public function getowner()
    {
        return $this->owner;
    }

    public function getrace()
    {
        return $this->race;
    }
    
    public function getquests()
    {
        return $this->quests;
    }

    /**
     * Zjisti zda je postava v danem questu
     * 
     * @param int $quest
     * @return bool
     */
    public function is_in_quest($quest)
    {
        if($quest < 1) throw new UnexpectedQuestIdException("Neočekávané id questu -- nelze zjistit zda je postava v questu.", "Neplatné id questu", "Neplatné id questu.", 6206);
        
        return(in_array($quest, $this->quests)); 
    }

    /**
     * Metoda pro nacteni seznamu questu postavy z DB
     * 
     * @return void
     */
    protected function load_quest()
    {
        if($this->character_id < 1) throw new UnexpectedCharacterIdException("Nelze nacist questy postavy, jelikoz jeji ID neni platne.", "Neplatné id postavy", "Neplatné id postavy.", 6205);
        global $rpgws_config;

        $this->quests = array();
        
        $query = "
            SELECT
                drd_quest_id
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quest_member
            WHERE
                drd_character_id = " . self::$m_DB->quote($this->character_id) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) return;
        
        foreach($result as $row)
        {
            array_push($this->quests, $row['drd_quest_id']);
        }
    }
    
    /**
     * Staticka metoda pro nacteni postavy
     *
     * @param int $id
     * @return DrD_Character_Model
     */
    public function load($id)
    {
        if($id < 1) throw new UnexpectedCharacterIdException("Nelze nacist postavu, jelikoz jeji ID neni platne.", "Neplatné id postavy", "Neplatné id postavy.", 6205);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        global $rpgws_config;
        
        $query = "
            SELECT
                *
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_characters
            WHERE
                drd_character_id = " . self::$m_DB->quote($id) . "
        ";
        
        $result = self::$m_DB->query($query);

        if(self::$m_DB->num_rows() < 1) throw new NonExistCharacterException("Postava s id $id neexistuje.", "Postava neexistuje.", "Pozadovana postava neni v databazi.", 6207);
        
        $result = $result[0];
        $char = new self();
        $char->class = DrD_Class_Model::load($result['class_id']);
        $char->race = DrD_Race_Model::load($result['race_id']);
        $char->description = $result['description'];
        $char->hit_points = $result['hit_points'];
        $char->character_id = $result['drd_character_id'];
        $char->items = $result['items'];
        $char->mana = $result['mana'];
        $char->name = $result['name'];
        $char->owner = $result['owner_id'];
        $char->load_quests();
        
        return $char;
    }

    /**
     * Nacte vsechny postavy daneho vlastnika
     * 
     * @param int $owner
     * @return array
     */
    public static function load_by_player($owner)
    {
        if($id < 1) throw new UnexpectedPlayerIdException("Nelze nacist postavy, jelikoz ID vlastnika neni platne.", "Neplatné id vlastníka", "Neplatné id vlastníka.", 6208);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        global $rpgws_config;
        
        $query = "
            SELECT
                *
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_characters
            WHERE
                owner_id = " . self::$m_DB->quote($owner) . "
        ";
        
        $result = self::$m_DB->query($query);
        $ret = array();
        if(self::$m_DB->num_rows() < 1) return null;
        
        $result = $result[0];
        foreach($result as $row)
        {
            $char = new self();
            $char->class = DrD_Class_Model::load($result['class_id']);
            $char->race = DrD_Race_Model::load($result['race_id']);
            $char->description = $result['description'];
            $char->hit_points = $result['hit_points'];
            $char->character_id = $result['drd_character_id'];
            $char->items = $result['items'];
            $char->mana = $result['mana'];
            $char->name = $result['name'];
            $char->owner = $result['owner_id'];
            $char->load_quests();
            $ret[$char->character_id] = $char;
        }
        
        return $ret;
    }

    /**
     * Metoda nacte vsechny postavy na danem questu
     *
     * @param int $quest
     * @return array
     */
    public static function load_by_quest($quest)
    {
        if($quest < 1) throw new UnexpectedQuestIdException("Nelze nacist postavy, jelikoz ID questu neni platne.", "Neplatné id questu", "Neplatné id questu.", 6209);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        
        global $rpgws_config;
        
        $query = "
            SELECT
                *
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_characters
            WHERE
                drd_character_id IN (
                    SELECT 
                        drd_character_id
                    FROM
                        " . $rpgws_config['db']['prefix'] . "drd_quest_members
                    WHERE
                        drd_quest_id = " . self::$m_DB->quote($quest) . ")
        ";
        
        $result = self::$m_DB->query($query);
        $ret = array();
        if(self::$m_DB->num_rows() < 1) return null;
        
        $result = $result[0];
        foreach($result as $row)
        {
            $char = new self();
            $char->class = DrD_Class_Model::load($result['class_id']);
            $char->race = DrD_Race_Model::load($result['race_id']);
            $char->description = $result['description'];
            $char->hit_points = $result['hit_points'];
            $char->character_id = $result['drd_character_id'];
            $char->items = $result['items'];
            $char->mana = $result['mana'];
            $char->name = $result['name'];
            $char->owner = $result['owner_id'];
            $char->load_quests();
            $ret[$char->character_id] = $char;
        }
        
        return $ret;
    }

    /**
     * Vyradi uzivatele z questu
     * 
     * @param int $quest
     * @return void
     */
    public function rem_from_quest($quest)
    {
        if($quest < 1) throw new UnexpectedQuestIdException("Nelze nacist postavy, jelikoz ID questu neni platne.", "Neplatné id questu", "Neplatné id questu.", 6209);
        
        $idx = array_search($quest, $this->quests);
        if($idx === false) return;
        
        unset($this->quest[$idx]);
        $this->q_changed = true;
    }

    /**
     * Pomocna metoda pro update postavy v DB
     * @return void
     */
    protected function update()
    {
        global $rpgws_config;
        if($this->character_id < 1) throw new UnexpectedCharacterIdException("Nelze nacist postavu, jelikoz jeji ID neni platne.", "Neplatné id postavy", "Neplatné id postavy.", 6210); 
        
        $class = ($this->class === null ? 0 : $this->class->class_id);
        $race = ($this->race === null ? 0 : $this->race->race_id);
        
        $query = "
            UPDATE
                " . $rpgws_config['db']['prefix'] . "drd_characters
            SET
                class_id = " . self::$m_DB->quote($class) . ",
                description = " . self::$m_DB->quote($this->description) . ",
                hit_points = " . self::$m_DB->quote($this->hit_points) . ",
                items = " . self::$m_DB->quote($this->items) . ",
                mana = " . self::$m_DB->quote($this->mana) . ",
                name = " . self::$m_DB->quote($this->name) . ",
                owner_id = " . self::$m_DB->quote($this->owner) . ",
                race_id = " . self::$m_DB->quote($race) . "
            WHERE
                drd_character_id = " . self::$m_DB->quote($this->character_id) . "          
        ";
        
        self::$m_DB->query($query);
        
        $this->save_quests();
    }
    
    /**
     * Pomocna metoda pro vlozeni postavy do DB
     * 
     * @return void
     */
    protected function insert()
    {
        global $rpgws_config;
        $class = ($this->class === null ? 0 : $this->class->class_id);
        $race = ($this->race === null ? 0 : $this->race->race_id);
        
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "drd_characters
                (class_id, description, hit_points, items, mana, name, owner_id, race_id)
            VALUES (
                " . self::$m_DB->quote($class) . ",
                " . self::$m_DB->quote($this->description) . ",
                " . self::$m_DB->quote($this->hit_points) . ",
                " . self::$m_DB->quote($this->items) . ",
                " . self::$m_DB->quote($this->mana) . ",
                " . self::$m_DB->quote($this->name) . ",
                " . self::$m_DB->quote($this->owner) . ",
                " . self::$m_DB->quote($race) . ")
        ";
        
        self::$m_DB->query($query);
        $this->save_quests();
    }
    
    /**
     * Metoda pro ulozeni postavy do DB
     * @return unknown_type
     */
    public function save()
    {
        if($this->character_id > 0) {
            $this->update();
        } else {
            $this->insert();
        }
    }
    
    /**
     * Protected metoda pro ulozeni vsech questu hrace
     * 
     * @access protected
     * @return void
     */
    protected function save_quests()
    {
        //zjistime zda je potreba questy ulozit
        if(!$this->q_changed) return;
         
        //spustime transakci
        self::$m_DB->query("BEGIN;");
        
        global $rpgws_config;
        try
        {
            //vymazeme questy hrace
            $query = "
                DELETE FROM
                    " . $rpgws_config['db']['prefix'] . "drd_quest_members
                WHERE
                    drd_character_id = " . self::$m_DB->quote($this->character_id) . "
            ";
            
            self::$m_DB->query($query);
            
            //vlozime aktualni questy
            $query = "
                INSERT INTO
                    " . $rpgws_config['db']['prefix'] . "drd_quest_members
                    (drd_quest_id, drd_character_id)
                VALUES
            ";     
            $sep = "";
            $cnt = 0;
            foreach($this->quests as $quest) 
            {
                $query .= "$sep(
                    " . self::$m_DB->quote($quest) . ",
                    " . self::$m_DB->quote($this->character_id) . ")
                ";
                $sep = ", ";
                $cnt++;
            }
            
            if($cnt > 0) self::$m_DB->query($query);
            
            //commit transakce
            self::$m_DB->query("COMMIT;");
        } catch(Exception $ex) {
            self::$m_DB->query("ROLLBACK;");
            throw $ex;
        }
    }

    /**
     * 
     * @param newVal
     */
    public function setclass($newVal)
    {
        $this->class = $newVal;
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
    public function sethit_points($newVal)
    {
        $this->hit_points = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setcharacter_id($newVal)
    {
        $this->character_id = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setitems($newVal)
    {
        $this->items = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setmana($newVal)
    {
        $this->mana = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setname($newVal)
    {
        $this->name = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setowner($newVal)
    {
        $this->owner = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setrace($newVal)
    {
        $this->race = $newVal;
    }

}
?>