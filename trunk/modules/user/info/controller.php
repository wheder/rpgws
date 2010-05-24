<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 22-V-2010 12:31:28
 */
class User_Info_Controller implements ControllerInterface
{

    private $m_View;
    private $m_Request;
    private $config;
    private $auth;
    
    function __construct()
    {
        include dirname(__FILE__) . "/../config/user_conf.php"; 
        $this->config = $user_config;
        $this->auth = new Authentificator();
    }


    

    public function edit_action()
    {
        $this->m_View->err = false;
        $user_id = $this->auth->logged_user();
        if($user_id < 1) {
            $this->m_View->err = true;
            $this->m_View->msg = "Nejste přihlášen.";
            $this->m_View->printPage();
            return;
        }

	    $user = new User_Model;
	    $user->load($user_id);
	        
	    $oldpass = $this->m_Request->get_param("oldpass", $this->config['password']['maxlength']);
	    $newpass = $this->m_Request->get_param("newpass", $this->config['password']['maxlength']);
	    $newpass2 = $this->m_Request->get_param("newpass2", $this->config['password']['maxlength']);
	    
	    if(!empty($oldpass))
	    {
	        $hash = sha1($user->nick . ":" . $oldpass);
	        if($hash != $user->pass) 
	        {
	            $this->m_View->err = true;
	            $this->m_View->msg = "Staré heslo není správné.";
	            $this->m_View->printPage();
	            return;
	        }
	        
	        if(strlen($newpass) < $this->config['password']['minlength'])
	        {
	            $this->m_View->err = true;
	            $this->m_View->msg = "Nové heslo je příliš krátké.";
	            $this->m_View->printPage();
	            return;
	        }
	        
	        if($newpass != $newpass2)
	        {
	            $this->m_View->err = true;
	            $this->m_View->msg = "Nová hesla se neshodují.";
	            $this->m_View->printPage();
	            return;
	        }
	        
	        $user->pass = sha1($user->nick . ":" . $newpass);
	    }
	    	    
	    $fields = $user->get_detail_types();
	    if(!empty($fields)) {
	        foreach($fields as $field) {
	            $user->set_detail($field, $this->m_Request->get_param($field, 255));
	            $user->set_public($field, ($this->m_Request->get_param_int("public_$field") == 1));
	        }
	    }
	    $user->save();
	    $this->m_View->err = false;
	    $this->m_View->msg = "Úspěšně uloženo.";
	    $this->m_View->printPage();
	}

    public function edit_form_action()
    {
        $this->m_View->err = false;
        $user_id = $this->auth->logged_user();
        if($user_id < 1) {
            $this->m_View->err = true;
            $this->m_View->msg = "Nejste přihlášen.";
        } else {
            $this->m_View->nick_max = $this->config['nick']['maxlength'];
            $this->m_View->pass_max = $this->config['password']['maxlength'];
            $user = new User_Model();
            $user->load($user_id);
            $this->m_View->user = $user;
            $this->m_View->fields = $user->get_detail_types();
        }
        $this->m_View->printPage();
    }
    
    public function show_user_action()
    {
        $logged = $this->auth->logged_user();
        $this->m_View->err = false;
        $nick = $this->m_Request->get_uri_string();

        if(empty($nick)) {
            $this->m_View->err = true;
            $this->m_View->msg = "Nevybral jste zadneho uzivatele";
            $this->m_View->printPage();
            return;
        }
        
        $user = new User_Model();
        $user->load(0, $nick);
        
        $this->m_View->logged = ($logged > 0);
        $this->m_View->user = $user;
        $fields = $user->get_detail_types();
        $details = array();
        foreach($fields as $field)
        {
	    $detail = $user->get_detail($field);
            if(($logged > 0 || $user->is_public($field)) && !empty($detail)){
                $details[$field] = $detail;
            }
        }
        $this->m_View->details = $details;
        $this->m_View->printPage();
    }

    /**
     * 
     * @param req
     */
    public function registerRequest(Request $req)
    {
        $this->m_Request = $req;
    }

    /**
     * 
     * @param view
     */
    public function registerView(View $view)
    {
        $this->m_View = $view;
    }

    public function show_list_action()
    {
        $this->m_View->err = false;
        $db = Db::get();
        $query = "
        	SELECT
        	    nick,
        	    COALESCE(last_action, 'offline') AS action,
        	FROM
        	    users
        ";
        
        $this->m_View->users = $db->query($query);

        if($db->num_rows() < 1) 
        {
            $this->m_View->err = true;
            $this->m_View->msg = "V databázi nejsou žádní uživatelé.";
        }
        
        $this->m_View->printPage();
    }
    
    public function index_action()
    {
        header("location: /user/info/show_list");
    }

}
?>
