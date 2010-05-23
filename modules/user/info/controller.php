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
        $auth = new Authentificator();
	}



	public function edit_action()
	{
	    
	}

	public function edit_form_action()
	{
	    $this->m_View->err = false;
	    $user_id = $this->auth->logged_user();
	    if($user_id < 1) {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Nejste přihlášen.";
	    } else {
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

}
?>