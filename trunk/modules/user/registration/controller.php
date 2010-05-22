<?php

/**
 * @author Michal Hyn�ica
 * @version 1.0
 * @created 22-V-2010 12:30:22
 */
class User_Registration_Controller implements ControllerInterface
{

	private $m_View;
	private $m_Request;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	public function register_action()
	{
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
	    $this->m_View->printPage();
	}

	public function index_action()
	{
	    $this->show_form_action();
	}
}
?>