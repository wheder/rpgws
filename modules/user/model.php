<?php


/**
 * @author Jakub Holý
 * @version 1.0
 * @created 22-V-2010 11:21:34
 */
class User_Model
{

    private $m_DB;
    private $born;
    private $confirmed;
    private $deleted;
    private $mail;
    private $nick;
    private $pass;
    private $user_id;
	private $last_action;
	private $last_ip;
    private $extended;
    
    function __construct()
    {
        $this->born = "";
        $this->confirmed = false;
        $this->deleted = false;
        $this->mail = "";
        $this->nick = "";
        $this->pass = "";
        $this->user_id = 0;
        $this->last_action = "";
        $this->last_ip = "";
        $this->m_DB = DB::get();
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
     * Funkce vygeneruje nove heslo
     * @param int $length
     * @return string
     */
    public function generate_password($length)
    {
        if($length < 1) return "";
        $charpool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        
        $result = "";
        for($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, strlen($charpool) - 1);
            $result .= substr($charpool, $rand, 1);
        }
        
        return $result;
    }

    public function getborn()
    {
        return $this->born;
    }

    public function getconfirmed()
    {
        return $this->confirmed;
    }

    public function getdeleted()
    {
        return $this->deleted;
    }

    public function getmail()
    {
        return $this->mail;
    }

    public function getnick()
    {
        return $this->nick;
    }

    public function getpass()
    {
        return $this->pass;
    }

    public function getuser_id()
    {
        return $this->user_id;
    }

    /**
     * Funkce nacte data o uzivateli z databaze
     *
     * @param int $id
     * @return void
     */
    public function load($id)
    {
        global $rpgws_config;
        if($id < 1) throw new UnexpectedUserIdException("Bylo pozadovano nacteni uzivatele s id = $id", "Neplatne ID", "Neplatne id uzivatele.", 2102);
        $query = "
            SELECT 
                *,
                INET_NTOA(last_ip) AS ip
            FROM
                " . $rpgws_config['db']['prefix'] . "users
            WHERE
                user_id = " . $this->m_DB->quote($id) . " 
        ";
        
        $result = $this->m_DB->query($query);
        if($this->m_DB->num_rows() < 1) throw new NonExistUserException("Uzivatel s id $id neni v DB.", "Uzivatel neexistuje", "Uzivatel s id $id neexistuje.", 2001);
        
        $this->user_id = $result[0]['user_id'];
        $this->born = $result[0]['born'];
        $this->confirmed = ($result[0]['confirmed'] == 1);
        $this->deleted = ($result[0]['deleted'] == 1);
        $this->mail = $result[0]['mail'];
        $this->nick = $result[0]['nick'];
        $this->pass = $result[0]['pass'];
        $this->last_action = $result[0]['last_action'];
        $this->last_ip = $result[0]['ip'];
        
        $this->load_extended($this->user_id);
    }
    
    /**
     * Funkce nacte detaily uctu z DB
     * @param int $id
     * @return void
     */
    private function load_extended($id)
    {
        global $rpgws_config;
        if($id < 1) throw new UnexpectedUserIdException("Bylo pozadovano nacteni uzivatele s id = $id", "Neplatne ID", "Neplatne id uzivatele.", 2102);
        
        $query = "
            SELECT
                detail_type.name,
                detail_type.user_detail_type_id,
                COALESCE(detail.value, '') AS value,
                COALESCE(detail.public, 0) AS public,  
            FROM
                (SELECT
                    *
                 FROM 
                     " . $rpgws_config['db']['prefix'] . "user_detail
                 WHERE
                     user_id = " . $this->m_DB->quote($id) . "
                 ) AS detail
            RIGHT OUTER JOIN
                " . $rpgws_config['db']['prefix'] . "user_detail_types AS detail_type
            USING
                user_detail_type_id
        ";
        
        $result = $this->m_DB->query($query);
        
        if($this->m_DB->num_rows() < 1) return;
        
        foreach($result as $row)
        {
            $this->extended[$row['name']] = $row;
        }
    }
    
    /**
     * Metoda pro ulozeni detailu do DB 
     * @return void
     */
    private function save_extended()
    {
        global $rpgws_config;
        if($this->user_id < 1) throw new UnexpectedUserIdException("Bylo pozadovano nacteni uzivatele s id = $id", "Neplatne ID", "Neplatne id uzivatele.", 2102);
        
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "user_detail
                (user_id, user_detail_type_id, value, public)
            VALUES";
        $sep = "";
        foreach($this->extended as $row) {
            if(!empty($row['value'])) {
                $query .=  "$sep
                    (
                        " . $this->m_DB->quote($this->user_id) . ",
                        " . $this->m_DB->quote($row['user_detail_type_id']) . ",
                        " . $this->m_DB->quote($row['value']) . ",
                        " . $this->m_DB->quote($row['public']) . "
                    )
                ";
                $sep = ",";
            }
        }
        $query .= "
            ON DUPLICATE KEY UPDATE
                value = VALUES(value),
                public = VALUES(public)
        ";

        $this->m_DB->query($query);
    }
    
    /**
     * Metoda vracejici seznam moznych detailu
     * @return array
     */
    public function get_detail_types()
    {
        $result = array();
        
        foreach($this->extended as $row) {
            array_push($result, $row['name']);
        }
        
        return $result;
    }
    
    /**
     * Metoda nastavi predany detail
     * @param string $name
     * @param string $value
     * @return void
     */
    public function set_detail($name, $value) {
        if(!isset($this->extended[$name])) throw new DetailDoesntExistsException("Modul se pokusil nastavit uživatelský detail $name, který neexistuje.", "Detail neexistuje.", "Uživatelský detail $name neexistuje.", 6101);
        
        $this->extended[$name]['value'] = $value;
    }
    
    /**
     * Metoda ziska uzivatelsky detail
     * @param string $name
     * @return string
     */
    public function get_detail($name) {
        if(!isset($this->extended[$name])) throw new DetailDoesntExistsException("Modul se pokusil přečíst uživatelský detail $name, který neexistuje.", "Detail neexistuje.", "Uživatelský detail $name neexistuje.", 6102);
        
        return $this->extended[$name]['value'];
    }
    
    /**
     * Metoda zjisti zda je dany detail verejny nebo soukromi
     * @param string $name
     * @return bool
     */
    public function is_public($name)
    {
        if(!isset($this->extended[$name])) throw new DetailDoesntExistsException("Modul se pokusil přečíst uživatelský detail $name, který neexistuje.", "Detail neexistuje.", "Uživatelský detail $name neexistuje.", 6102);
        
        return ($this->extended[$name]['public'] == 1);
    }

    /**
     * funkce updatne zaznam o uzivateli v DB
     * 
     * @return void
     */
    private function update()
    {
        if($this->user_id < 1) UnexpectedUserIdException("Bylo pozadovano nacteni uzivatele s id = $id", "Neplatne ID", "Neplatne id uzivatele.", 2102);
        global $rpgws_config;
        
        $query = "
            UPDATE
        	    " . $rpgws_config['db']['prefix'] . "users
        	SET
        	    born = " . $this->m_DB->quote($this->born) . "
        	    confirmed = " . $this->m_DB->quote(($this->confirmed ? 1 : 0)) . ",
        	    deleted = " . $this->m_DB->quote(($this->deleted ? 1 : 0)) . ",
        	    mail = " . $this->m_DB->quote($this->mail) . ",
        	    nick = " . $this->m_DB->quote($this->nick) . ",
        	    pass = " . $this->m_DB->quote($this->pass) . ",
        	    last_action = " . $this->m_DB->quote($this->last_action) . ",
        	    last_ip = INET_ATON(" . $this->m_DB->quote($this->last_ip) . ")
        	WHERE
        	    user_id = " . $this->m_DB->quote($this->user_id) . "
        ";
        
        $this->m_DB->query($query);
    } 
    
    /**
     * Funkce vlozi data o uzivateli do DB jako novy zaznam
     * @return void
     */
    private function insert()
    {
        global $rpgws_config;
        
        $query = "
            INSERT INTO
                " . $rpgws_config['db']['prefix'] . "users
                (born, confirmed, deleted, mail, nick, pass, last_action, last_ip)
            VALUES(
            	" . $this->m_DB->quote($this->born) . ",
            	" . $this->m_DB->quote(($this->confirmed ? 1 : 0)) . ",
            	" . $this->m_DB->quote(($this->deleted ? 1 : 0)) . ",
            	" . $this->m_DB->quote($this->mail) . ",
            	" . $this->m_DB->quote($this->nick) . ",
            	" . $this->m_DB->quote($this->pass) . ",
            	" . $this->m_DB->quote($this->last_action) . ",
            	INET_ATON(" . $this->m_DB->quote($this->last_ip) . "))
        ";
        
        $this->m_DB->query($query);
    }
    /**
     * Funkce ulozi data o uzivateli do DB 
     * @return void
     */
    public function save()
    {
        if($this->user_id < 1)
        {
            $this->insert();
        } else {
            $this->update();
            $this->save_extended();
        } 
    }

    /**
     * 
     * @param newVal
     */
    public function setborn($newVal)
    {
        $this->born = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setconfirmed($newVal)
    {
        $this->confirmed = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setdeleted($newVal)
    {
        $this->deleted = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setmail($newVal)
    {
        $this->mail = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setnick($newVal)
    {
        $this->nick = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setpass($newVal)
    {
        $this->pass = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setuser_id($newVal)
    {
        $this->user_id = $newVal;
    }

	public function getlast_action()
	{
		return $this->last_action;
	}

	public function getlast_ip()
	{
		return $this->last_ip;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setlast_action($newVal)
	{
		$this->last_action = $newVal;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setlast_ip($newVal)
	{
		$this->last_ip = $newVal;
	}

}
?>