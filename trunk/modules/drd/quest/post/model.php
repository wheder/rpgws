<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:43:49
 */
class DrD_Quest_Post_Model
{

    private $author_character;
    private $author_user;
    private $content;
    private $post_id;
    private $quest_id;
    private $time;
    private $whisper_to;
    private $whisper;
    private static $m_DB = null;

    function __construct()
    {
        $this->author_character = null;
        $this->author_user = 0;
        $this->content = "";
        $this->post_id = 0;
        $this->quest_id = 0;
        $this->time = "0000-00-00 00:00:00";
        $this->whisper_to = array();
        $this->whisper = false;
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
     * Prida uzivatele do seznamu pro koho je whisp urceny
     * 
     * @param int $user
     */
    public function add_whisp_to($char)
    {
        if($user < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze pridat whisp.", "Neplatné id postavy", "Neplatné id postavy.", 6220);

        array_push($this->whisper_to, $char);
    }

    public function getauthor_character()
    {
        return $this->author_character;
    }

    public function getauthor_user()
    {
        return $this->author_user;
    }

    public function getcontent()
    {
        return $this->content;
    }

    public function getpost_id()
    {
        return $this->post_id;
    }

    public function getquest_id()
    {
        return $this->quest_id;
    }

    public function gettime()
    {
        return $this->time;
    }
    
    public function is_whisper()
    {
        return $this->whisper;
    }

    /**
     * Metoda zjisti, zda je whisp urceny pro postavu predanou v parametru
     * 
     * @param int $user
     * @return bool
     */
    public function is_whisp_to($user)
    {
        if($user < 1) throw new UnexpectedCharacterIdException("Neočekávané id postavy -- nelze overit whisp.", "Neplatné id postavy", "Neplatné id postavy.", 6221);
        
        return(in_array($user, $this->whisper_to));
    }

    /**
     * Metoda nacte prispevek z databaze
     *
     * @param int $id
     * @return DrD_Quest_Post_Model
     */
    public static function load($id)
    {
        if($user < 1) throw new UnexpectedPostIdException("Neočekávané id prispevku -- nelze načíst z DB.", "Neplatné id příspěvku", "Příspěvek nebyl načten.", 6222);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        global $rpgws_config;
        
        $query = "
            SELECT
                *,
                COALESCE(author_character_id, 0) AS author_character,
                COALESCE(author_user_id, 0) AS author_user
            FROM 
                " . $rpgws_config['db']['prefix'] . "drd_quest_posts
            WHERE
                drd_quest_post_id = " . self::$m_DB->quote($id) . "
        ";
        
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) throw new NonExistsPostException("Pozadovany post s id $id neexistuje", "Příspěvek neexistuje", "Požadovaný příspěvek není v DB.", 6223);

        $result = $result[0];
        
        $post = new self();
        $post->author_character = ($result['author_character'] == 0 ? null : DrD_Character_Model::load($result['author_character']));
        $post->author_user = $result['author_user'];
        $post->content = $result['content'];
        $post->post_id = $result['drd_quest_post_id'];
        $post->quest_id = $result['belongs_to_quest_id'];
        $post->time = $result['origin_time'];
        $post->whisper = ($result['is_whisper'] == 1);
        
        $post->load_whisps();
        
        return $post;
    }

    /**
     * Metoda nacte vsechny prispevky daneho questu
     * 
     * @param int $quest
     * @return array
     */
    public static function load_all_by_quest($quest)
    {
        if($quest < 1) throw new UnexpectedQuestIdException("Neočekávané id questu -- nelze načíst příspěvky z DB.", "Neplatné id questu", "Příspěvky questy nebyly načten.", 6224);
        if(self::$m_DB === null) self::$m_DB = Db::get();
        global $rpgws_config;
        
        $query = "
            SELECT
                *,
                COALESCE(author_character_id, 0) AS author_character,
                COALESCE(author_user_id, 0) AS author_user
            FROM 
                " . $rpgws_config['db']['prefix'] . "drd_quest_posts
            WHERE
                belongs_to_quest_id = " . self::$m_DB->quote($quest) . "
            ORDER BY
                origin_time DESC
        ";
        
        $result = self::$m_DB->query($query);
        
        $ret = array();
        if(self::$m_DB->num_rows() < 1) return $ret;
        
        foreach($result as $row) {
            $post = new self();
            $post->author_character = ($row['author_character'] == 0 ? null : DrD_Character_Model::load($row['author_character']));
            $post->author_user = $row['author_user'];
            $post->content = $row['content'];
            $post->post_id = $row['drd_quest_post_id'];
            $post->quest_id = $row['belongs_to_quest_id'];
            $post->time = $row['origin_time'];
            $post->whisper = ($row['is_whisper'] == 1);
            
            $post->load_whisps();
        
            array_push($ret, $post);
        }

	return $ret;
    }

    /**
     * Pomocna metoda pro nacteni cilu septani
     * 
     * @access protected
     * @return void
     */
    protected function load_whisps()
    {
        if($this->post_id < 1) throw new UnexpectedPostIdException("Neočekávané id prispevku -- nelze načíst seznam příjemců z DB.", "Neplatné id příspěvku", "Seznam příjemců nebyl načten.", 6225);
        global $rpgws_config;
        $query = "
            SELECT 
               written_for_drd_character_id
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quest_whisper
            WHERE
                drd_quest_post_id = " . self::$m_DB->quote($this->post_id) . "
        ";
        
        $this->whisper_to = array();
        $result = self::$m_DB->query($query);
        
        if(self::$m_DB->num_rows() < 1) return;
        
        foreach($result as $row) {
            array_push($this->whisper_to, $row['written_for_drd_character_id']);
        }
    }

    /**
     * Pomocna metoda pro update prispevku v DB
     * 
     * @return void
     */
    protected function update()
    {
        if($this->post_id < 1) throw new UnexpectedPostIdException("Neočekávané id prispevku -- nelze uložit příspěvek do DB.", "Neplatné id příspěvku", "Příspěvek nebyl uložen.", 6226);
        global $rpgws_config;
        
        $author_char = ($this->author_character === null ? 0 : $this->author_character->character_id);
        $whisp = ($this->whisper ? "b'1'" : "b'0'");
        $query = "
            UPDATE
                " . $rpgws_config['db']['prefix'] . "drd_quest_posts
            SET
                author_character_id = " . self::$m_DB->quote($author_char) . ",
                author_user_id = " . self::$m_DB->quote($this->author_user) . ",
                content = " . self::$m_DB->quote($this->content) . ",
                belongs_to_quest_id = " . self::$m_DB->quote($this->quest_id) . ",
                origin_time = " . self::$m_DB->quote($this->time) . ",
                is_whisper = " . $whisp . "
            WHERE
                drd_quest_post_id = " . self::$m_DB->quote($this->post_id) . "
        ";
        
        self::$m_DB->query($query);
    }
    
    /**
     * Pomocna metoda pro vlozeni noveho prispevku do DB
     * 
     * @return void
     */
    protected function insert()
    {
        global $rpgws_config;
        
        $author_char = ($this->author_character === null ? "NULL" : self::$m_DB->quote($this->author_character->character_id));
        $whisp = ($this->whisper ? "b'1'" : "b'0'");
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "drd_quest_posts
                (author_character_id, author_user_id, content, belongs_to_quest_id, origin_time, is_whisper)
            VALUES (
                " . $author_char . ",
                " . self::$m_DB->quote($this->author_user) . ",
                " . self::$m_DB->quote($this->content) . ",
                " . self::$m_DB->quote($this->quest_id) . ",
                NOW(),
                " . $whisp . ")
        ";
        
        self::$m_DB->query($query);
        $this->post_id = self::$m_DB->last_insert_id();
        $this->save_whisps();
    }
    /**
     * Metoda pro ulozeni prispevku
     * 
     * @return void
     */
    public function save()
    {
        if($this->post_id > 0) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * pomocna met
     * @return unknown_type
     */
    protected function save_whisps()
    {
        global $rpgws_config;
        if(empty($this->whisper_to)) return;
        
        $query = "
            INSERT IGNORE INTO
                " . $rpgws_config['db']['prefix'] . "drd_quest_whisper
                (drd_quest_post_id, written_for_drd_character_id)
            VALUES 
        ";
        
        $sep = "";
        $cnt = 0;
        foreach($this->whisper_to as $whisp) {
            $query .= "$sep(
                 " . self::$m_DB->quote($this->post_id) . ",
                 " . self::$m_DB->quote($whisp) . ")
            ";
            $sep = ", ";
            $cnt++;
        }
        
        if($cnt > 0) self::$m_DB->query($query);
    }

    /**
     * 
     * @param newVal
     */
    public function setauthor_character($newVal)
    {
        $this->author_character = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setauthor_user($newVal)
    {
        $this->author_user = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setcontent($newVal)
    {
        $this->content = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setpost_id($newVal)
    {
        $this->post_id = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setquest_id($newVal)
    {
        $this->quest_id = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function settime($newVal)
    {
        $this->time = $newVal;
    }
    
    public function setwhisper($newVal) 
    {
        $this->whisper = $newVal;
    }

}
?>
