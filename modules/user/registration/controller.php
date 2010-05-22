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
	    $this->m_Request =
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