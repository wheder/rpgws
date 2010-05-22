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
    private $unsucc_login;
    private $user_id;
	private $last_action;
	private $last_ip;

    function __construct()
    {
        $this->born = "";
        $this->confirmed = false;
        $this->deleted = false;
        $this->mail = "";
        $this->nick = "";
        $this->pass = "";
        $this->unsucc_login = 0;
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

    public function getunsucc_login()
    {
        return $this->unsucc_login;
    }

    public function getuser_id()
    {
        return $this->user_id;
    }

    /**
     * Funkce nacte data o uzivateli z databaze
     *
     * @param id
     * @return void
     */
    public function load(int $id)
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
        $this->unsucc_login = $result[0]['unsuccessful_login_atempts'];
        $this->last_action = $result[0]['last_action'];
        $this->last_ip = $result[0]['ip'];
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
        	    unsuccessful_login_atempts = " . $this->m_DB->quote($this->unsucc_login) . ",
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
                (born, confirmed, deleted, mail, nick, pass, unsuccessful_login_attempts, last_action, last_ip)
            VALUES(
            	" . $this->m_DB->quote($this->born) . ",
            	" . $this->m_DB->quote(($this->confirmed ? 1 : 0)) . ",
            	" . $this->m_DB->quote(($this->deleted ? 1 : 0)) . ",
            	" . $this->m_DB->quote($this->mail) . ",
            	" . $this->m_DB->quote($this->nick) . ",
            	" . $this->m_DB->quote($this->pass) . ",
            	" . $this->m_DB->quote($this->unsucc_login) . ",
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
    public function setnick(string $newVal)
    {
        $this->nick = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setpass(string $newVal)
    {
        $this->pass = $newVal;
    }

    /**
     * 
     * @param newVal
     */
    public function setunsucc_login($newVal)
    {
        $this->unsucc_login = $newVal;
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