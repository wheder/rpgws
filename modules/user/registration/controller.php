<?php

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 22-V-2010 12:30:22
 */
class User_Registration_Controller implements ControllerInterface
{

	private $m_View;
	private $m_Request;
        private $config = Array();

	function __construct()
	{
            include dirname(__FILE__) . "/../config/user_conf.php"; 
            $this->config = $user_config;
	}

	function __destruct()
	{
	}



	public function register_action()
	{
	    $nick = $this->m_Request->get_string("nick", $this->config['nick']['maxlength']);
	    $mail = $this->m_Request->get_string("mail", $this->config['mail']['maxlength']);
	    
	    if(sizeof($nick) < $this->config['nick']['minlength']) 
	    {
	        $this->m_View->err = true;
	        $this->m_View->emsg = "Uživatelské jméno je příliš krátké.";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    if($this->config['nick']['regexp']['match_required'] && !preg_match($this->config['nick']['regexp']['content'], $nick))
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Uživatelské jméno obsahuje nepovolené znaky";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    if(sizeof($mail) < $this->config['mail']['minlength'])
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Email je příliš krátký.";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    if($this->config['mail']['regexp']['match_required'] && !preg_match($this->config['mail']['regexp']['content'], $mail))
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Uživatelské jméno obsahuje nepovolené znaky";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    $user = new User_Model();
	    $user->nick = $nick;
	    $user->mail = $mail;
	    $pass = $user->generate_password($config['password']['generated_length']);
	    $user->pass = sha1($nick . ":" . $pass);
	    $user->last_ip = $_SERVER['REMOTE_ADDR'];
	    $user->save();
	    $this->m_View->msg = "Uživatel $nick byl úspěšně registrován. Heslo: $pass (<-- remove that! <--)";
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

	public function show_form_action()
	{
	    //global $user_config;
	    
            
	    $this->m_View->nick_max = $this->config['nick']['maxlength'];
	    $this->m_View->pass_max = $this->config['password']['maxlength'];
	    $this->m_View->mail_max = $this->config['mail']['maxlength'];
	    $this->m_View->printPage();
	}

	public function index_action()
	{
	    header("location: /user/registration/show_form");
	}
}
?>