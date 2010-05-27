<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:45:48
 */
class DrD_Class_Model
{

    private $class_id;
    private $description;
    private $name;
    private $parent;
    private static $m_DB;
    private static $loaded = array();
  
    function __construct()
    {
        $this->class_id = 0;
        $this->description = "";
        $this->name = "";
        $this->parent = null;
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
        return (!empty($var));
    }

    public function getclass_id()
    {
        return $this->class_id;
    }

    public function getdescription()
    {
        return $this->description;
    }

    public function getname()
    {
        return $this->name;
    }

    public function getparent()
    {
        return $this->parent;
    }

    /**
     * Nacte povolani z DB podle id
     * 
     * @param int $id
     * @return DrD_Class_Model
     */
    public static function load($id)
    {
        if($id < 1) throw new UnexpectedClassIdException("Nelze nacist povolani, jelikoz jeho ID neni platne.", "Neplatné id povolani", "Neplatné id povolani.", 6201);
        global $rpgws_config;
        if(isset(self::$loaded[$id])) return self::$loaded[$id];
        
        $query = "
        	SELECT
        	    *,
        	    COALESCE(parent_id, 0) AS parent
        	FROM
        		" . $rpqws_config['db']['prefix'] . "drd_classes
            WHERE
            	drd_classes_id = " . self::$m_DB->quote($id) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) throw new DrDClassDoesntExistException("Modul DrD se pokusil načíst třídu s id = $id, která neexisuje.", "Třída neexistuje.", "Třída s id $id neexistuje.", 6201);
        $result = $result[0];
        $class = new self();
        $class->class_id = $result['drd_classes_id'];
        $class->description = $result['description'];
        $class->name = $result['name'];
        if($result['parent'] > 0)
        {
            $class->parent = self::load($result['parent']);
        } else {
            $class->parent = null;
        }
        
        self::$loaded[$id] = $class;
        self::$loaded[$class->name] = $class;
        return $class;
    }

    /**
     * Nacte vsechny povolani z DB
     * 
     * @return array
     */
    public static function load_all()
    {
        global $rpgws_config;
        $query = "
        	SELECT
        	    *,
        	    COALESCE(parent_id, 0) AS parent
        	FROM
        	    " . $rpgws_config['db']['prefix'] . "drd_classes
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows < 1) return null;
        
        $parents = array();
        foreach($result as $row) 
        {
            $class = new self();
            $class->class_id = $row['drd_classes_id'];
            $class->description = $row['description'];
            $class->name = $row['name'];
            $class->parent = null;
            if($row['parent'] > 0)
            {
                $parents[$class->id] = $row['parent'];   
            }
            self::$loaded[$class->class_id] = $class;
            self::$loaded[$class->name] = $class; 
        }
        
        foreach($parents as $child_id => $parent_id)
        {
            self::$loaded[$child_id]->parent = self::$loaded[$parent_id];       
        }
        
        return self::$loaded;
    }

    /**
     * Metoda nacte povolani z db podle jeho jmena
     * @param string $name
     */
    public static function load_by_name($name)
    {
        if(isset(self::$loaded[$name])) return self::$loaded[$name];
        
        global $rpgws_config;
        if(isset(self::$loaded[$id])) return self::$loaded[$id];
        
        $query = "
        	SELECT
        	    *,
        	    COALESCE(parent_id, 0) AS parent
        	FROM
        		" . $rpqws_config['db']['prefix'] . "drd_classes
            WHERE
            	name = " . self::$m_DB->quote($name) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) throw new DrDClassDoesntExistException("Modul DrD se pokusil načíst třídu $name, která neexisuje.", "Třída neexistuje.", "Třída $name neexistuje.", 6202);
        $result = $result[0];
        $class = new self();
        $class->class_id = $result['drd_classes_id'];
        $class->description = $result['description'];
        $class->name = $result['name'];
        if($result['parent'] > 0)
        {
            $class->parent = self::load($result['parent']);
        } else {
            $class->parent = null;
        }
        
        self::$loaded[$class->class_id] = $class;
        self::$loaded[$class->name] = $class;
        return $class;
    }

    /**
     * Metoda ulozi informace do databaze
     * @return void
     */
    public function save()
    {
        if($this->class_id < 1) 
        {
            $this->insert();
        } 
        else
        {
            $this->update();    
        }
    }
    
    /**
     * Privatni metoda pro insert tridy do databaze
     * 
     * @access protected
     * @return void
     */
    protected function insert()
    {
        global $rpgws_config;
        
        $parent = ($this->parent === null ? 0 : $this->parent->class_id);
        $query = "
        	INSERT INTO
        		" . $rpgws_config['db']['prefix'] . "drd_classes
        	    (parent_id, name, description)
        	VALUES(
        		" . self::$m_DB->quote($parent) . ",
        		" . self::$m_DB->quote($this->name) . ",
        		" . self::$m_DB->quote($this->description) . ")
        ";
        
        self::$m_DB->query($query);
        $this->class_id = self::$m_DB->last_insert_id();
    }
    
    /**
     * Privatni metoda pro update tridy v db
     * 
     * @access protected
     * @return void
     */
    protected function update()
    {
        $parent = ($this->parent === null ? 0 : $this->parent->class_id);
        global $rpgws_config;
        
        $query = "
            UPDATE
                " . $rpgws_config['db']['prefix'] . "drd_classes
            SET
                parent_id = " . self::$m_DB->quote($parent) . ",
                name = " . self::$m_DB->quote($this->name) . ",
                description = " . self::$m_DB->quote($this->description) . "
            WHERE
                drd_classes_id = " . self::$m_DB->quote($this->class_id). "
        ";
        
        self::$m_DB->query($query);
    }
    
    /**
     * 
     * @param newVal
     */
    public function setclass_id($newVal)
    {
        $this->class_id = $newVal;
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
    public function setparent(DrD_Class_Model $newVal)
    {
        $this->parent = $newVal;
    }

}
?>