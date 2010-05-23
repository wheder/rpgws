<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 22-V-2010 12:29:46
 */
class User_Login_Controller implements ControllerInterface
{

	private $m_View;
	private $m_Request;
    private $config;	

	function __construct()
	{
	    include dirname(__FILE__) . "/../config/user_conf.php"; 
        $this->config = $user_config;
	}

	function __destruct()
	{
	}



	public function login_action()
	{
	    $nick = $this->m_Request->get_param("nick", $this->config['nick']['maxlength']);
	    $pass = $this->m_Request->get_param("pass", $this->config['pass']['maxlength']);
	    
	    if(strlen($nick) < $this->config['nick']['minlength']) 
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Uživatelské jméno je příliš krátké.";
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
	    
	    if(strlen($pass) < $this->config['mail']['minlength'])
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Email je příliš krátký.";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    $auth = new Authentificator();
	    try
	    {
	        $auth->login($nick, $pass);
	        $this->m_View->err = false;
	        $this->m_View->msg = "Uzivatel uspesne prihlasen";
	    }
	    catch (Exceptions $ex)
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = $ex->get_info();
	    }
	    $this->m_View->printPage();
	       
	}

	public function form_action()
	{
	    $this->m_View->nick_max = $this->config['nick']['maxlength'];
	    $this->m_View->pass_max = $this->config['password']['maxlength'];
	    $this->m_View->printPage();
	}

	public function logout_action()
	{
	    $auth = new Authentificator();
	    $auth->logout();
	    header("location: /");
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

	public function index_action()
	{
	    header('location: /user/login/form');
	}
}
?>