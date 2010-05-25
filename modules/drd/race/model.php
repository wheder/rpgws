<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:45:18
 */
class DrD_Race_Model
{

    private $description;
    private $name;
    private $race_id;
    private static $m_DB;
    private static $loaded = array();

    function __construct()
    {
        $this->description = "";
        $this->name = "";
        $this->race_id = 0;
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

    public function getdescription()
    {
        return $this->description;
    }

    public function getname()
    {
        return $this->name;
    }

    public function getrace_id()
    {
        return $this->race_id;
    }

    /**
     * Metoda nacte rasu z DB
	 *
     * @param int $id
     * @return DrD_Race_Model
     */
    public static function load($id)
    {
        if(isset(self::$loaded[$id])) return self::$loaded[$id];
        
        global $rpgws_config;
        $query = "
            SELECT
                *
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_races
            WHERE
                drd_races_id = " . self::$m_DB->quote($id) . "
        ";
        
        $result = self::$m_DB->query($query);
        if(self::$m_DB->num_rows() < 1) throw new DrDClassDoesntExistException("Modul DrD se pokusil načíst rasu s id = $id, která neexisuje.", "Rasa neexistuje.", "Rasa s id $id neexistuje.", 6203);
        
        $result = $result[0];
        $race = new self();
        $race->race_id = $result['drd_races_id'];
        $race->name = $result['name'];
        $race->description = $result['description'];
        
        self::$loaded[$race->race_id] = $race;
        self::$loaded[$race->name] = $race;
        
        return $race;
    }

    public static function load_all()
    {
    }

    /**
     * Metoda nacte rasu z DB podle jmena
     * 
     * @param string $name
     * @return DrD_Race_Model
     */
    public static function load_by_name($name)
    {
        if(isset(self::$loaded[$name])) return self::$loaded[$name];
        
        global $rpgws_config;
        $query = "
            SELECT
                *
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_races
            WHERE
                name = " . self::$m_DB->quote($name) . "
        ";
        
        $result = self::$m_DB->query($query);
        if(self::$m_DB->num_rows() < 1) throw new DrDClassDoesntExistException("Modul DrD se pokusil načíst rasu $name, která neexisuje.", "Rasa neexistuje.", "Rasa $name neexistuje.", 6204);
        
        $result = $result[0];
        $race = new self();
        $race->race_id = $result['drd_races_id'];
        $race->name = $result['name'];
        $race->description = $result['description'];
        
        self::$loaded[$race->race_id] = $race;
        self::$loaded[$race->name] = $race;
        
        return $race;
    }

    /**
     * Metoda ulozi model rasy do DB
     *
     * @return void
     */
    public function save()
    {
        if($this->race_id < 1) 
        {
            $this->insert();
        } 
        else
        {
            $this->update();
        } 
    }
    
    /**
     * Privatni metoda pro vlozeni nove rasy do DB
     * 
     * @access private
     * @return void
     */
    private function insert()
    {
        global $rpgws_config;
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "drd_races
                (name, description)
            VALUES (
                " . self::$m_DB->quote($this->name) . ",
                " . self::$m_DB->quote($this->description) . ")
        ";
        
        self::$m_DB->query($query);
    }
    
    /**
     * Privatni metoda pro update rasy v DB
     *
     * @access private
     * @return void
     */
    private function update()
    {
        global $rpgws_config;
        $query = "
            UPDATE
                " . $rpgws_config['db']['prefix'] . "drd_races
            SET
                name = " . self::$m_DB->quote($this->name) . ",
                description = " . self::$m_DB->quote($this->description) . "
            WHERE
                drd_races_id = " . self::$m_DB->quote($this->race_id) . "
        ";
        
        self::$m_DB->query($query);
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
    public function setname($newVal)
    {
        $this->name = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setrace_id($newVal)
    {
        $this->race_id = $newVal;
    }

}
?>