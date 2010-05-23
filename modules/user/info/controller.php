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
	        
	    $nick = $this->m_Request->get_param("nick", $this->config['nick']['maxlength']);
	    $oldpass = $this->m_Request->get_param("oldpass", $this->config['password']['maxlength']);
	    $newpass = $this->m_Request->get_param("newpass", $this->config['password']['maxlength']);
	    $newpass2 = $this->m_Request->get_param("newpass2", $this->config['password']['maxlength']);
	    
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

	public function show_info_action()
	{
	}
	
	public function index_action()
	{
	    header("location: /user/info/show_info");
	}

}
?>