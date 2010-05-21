<?php

/**
 * @author gambler
 * @version 1.0
 * @created 19-V-2010 14:18:22
 */
class Authentificator
{

    private $m_DB;

    function __construct()
    {
        $this->m_DB = Db::get(); 
    }

    function __destruct()
    {
    }

    private function last_action($user_id)
    {
        global $rpgws_config;
        $query = "UPDATE " . $rpgws_config['db']['prefix'] . "users SET last_action = NOW() WHERE user_id = ";
        $query .= $this->m_DB->quote($user_id) . " LIMIT 1"; 
        
        $this->m_DB->query($query);
    }
    /**
     * Zapise cas posledni akce do tabulky users nebo zaloguje (pokus o) prihlaseni
     * 
     * @param int
     * @param bool
     * @param bool
     * @return void
     */                                   
    private function do_log($user_id, $success = true)
    {
        global $rpgws_config;
        
        if($rpgws_config['logs']['level'] < 1) return;
        if($success && $rpgws_config['logs']['level'] < 2) return;
        
        $succ = ($success ? 1 : 0);
        $query = "INSERT INTO " . $rpgws_config['db']['prefix'] . "login_log (user_id, ip, time, success) VALUES (";
        $query .= $this->m_DB->quote($user_id);
        $query .= ", INET_ATON(" . $this->m_DB->quote($_SERVER['REMOTE_ADDR']). ")";
        $query .= ", NOW()";
        $query .= ", " . $this->m_DB->quote($succ);
        $query .= ")";
        
        $this->m_DB->query($query);           
    }

    /**
     * Zaregistruje session promene a nastavi pocatecni hodnotu last_login a user_ip
     * pri prihlaseni uzivatele
     * 
     * @param array
     * @return void
     */                             
    private function create_session($sql_result)
    {  
        $this->set_session($sql_result);
        
        session_regenerate_id(true);
    }
    
    
    /**
     * Nastavi hodnoty session z vysledku dotazu na DB
     * 
     * @param array
     * @return void
     */                        
    private function set_session($sql_result)
    {
        $_SESSION['user_id'] = $sql_result['user_id'];
        $_SESSION['nick'] = $sql_result['nick'];
    }
    
    /**
     * Zkontroluje a pri pripadne zmene znovu nacte uzivatelova data z DB
     * 
     * @return bool
     */                   
    private function check_session()
    {
        global $rpgws_config;
        $query = "SELECT INET_NTOA(last_ip) AS ip FROM " . $rpgws_config['db']['prefix'] . "users AS users JOIN "  . $rpgws_config['db']['prefix'] . "login_log AS log ON (users.user_id = log.user_id) WHERE users.user_id = ";
        $query .= $this->m_DB->quote($_SESSION['user_id']) . " AND log.success = 1 ORDER BY time DESC LIMIT 1";
        
        $result = $this->m_DB->query($query);
        if(!$result || $this->m_DB->num_rows() < 1)
            throw new NonExistUserException("Uzivatel $username neni v DB.", "Uzivatel neexistuje", "Uzivatel $username neexistuje.", 2001);
        $result = $result[0];
        
        if($_SERVER['REMOTE_ADDR'] != $result['ip']) return false;
    }

    /**
     * Nacte data uzivatele z DB
     * 
     * @param string
     * @param int
     * @return array
     */                             
    private function load_user_data($username, $id = 0)
    {
        global $rpgws_config;
        if($id == 0) {
            $query = "SELECT * FROM " . $rpgws_config['db']['prefix'] . "users WHERE nick = " . $this->m_DB->quote($username) . " LIMIT 1";
        } else {
            $query = "SELECT * FROM " . $rpgws_config['db']['prefix'] . "users WHERE user_id = " . $this->m_DB->quote($id) . " LIMIT 1";
        }
        $result = $this->m_DB->query($query);
        if(!$result || $this->m_DB->num_rows() < 1)
            throw new NonExistUserException("Uzivatel $username neni v DB.", "Uzivatel neexistuje", "Uzivatel $username neexistuje.", 2001);
        return $result[0];
    }
    /**
     * Kontrola jestli je uživatel přihlášen.
     * Pokud není vrátí 0, jinak vrátí id přihlášeného uživatele          
     * 
     * @return int
     */     
    public function logged_user()
    {   
        if(!isset($_SESSION['user_id'])) return 0;
        
        if(!$this->check_session()) return 0;
        
        $this->last_action($_SESSION['user_id']);
        
        return $_SESSION['user_id']; 
    }

    /**
     * Přihlásí uživatele
     *      
     * @param string password
     * @param string username
     * @return bool     
     */
    public function login($username, $password)
    {
        $result = $this->load_user_data($username);
        
        $pass = sha1($username . ":" . $password);
        try {
            if($result['pass'] != $pass)
                throw new PasswordDoesntMatchException("Prihlaseni uzivatele $username se nepodarilo, protoze se neshoduje hash hesla. Hash poslaneho hesla = $pass, hash v db = " . $result['pass'], "Nepodarilo se prihlasit", "Spatne heslo", 2002);
        
            if($result['deleted'] > 0)
                throw new DeletedAccountException("Pokus o prihlaseni na smazanou postavu $username.", "Účet smazán", "Přihlášení se nepodařilo, účet byl smazán.", 2003);
            
            if($result['confirmed'] == 0)
                throw new NotConfirmedException("Pokus o prihlaseni na neschvaleny ucet $username.", "Účet nebyl schválen", "Přihlášení selhalo, jelikož účet nebyl schválen.", 2004);
        } catch (Exceptions $ex) {
            $this->do_log($result['user_id'], false);
            throw $ex;
        }
        
        //zapsani last_ip
        global $rpgws_config;
        $query = "UPDATE " . $rpgws_config['db']['prefix'] . "users";
        $query = " SET last_action = NOW(), ";
        $query = " last_ip = INET_ATON(" . $this->m_DB->quote($_SERVER['REMOTE_ADDR']) . ")";
        $query = " WHERE user_id = " . $this->m_DB->quote($result['user_id']);                 
        $this->create_session($result);
        
        $this->do_log($result['user_id'], true);
        
        return true;
    }

    /**
     * Odhlaseni uzivatele
     * 
     * @return void
     */                   
    public function logout()
    {
        if($this->logged_user() > 0) {
            session_destroy();
            session_regenerate_id(true);
        }
    }

}
?>