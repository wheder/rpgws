<?php

/**
 * @author Michal Hyn�ica
 * @version 1.0
 * @created 22-V-2010 12:29:46
 */
class User_Login_Controller implements ControllerInterface
{

	private $m_View;
	private $m_Request;
	

	function __construct()
	{
	}

	function __destruct()
	{
	}



	public function login_action()
	{
	}

	public function form_action()
	{
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
	    $this->form_action();
	}
}
?>