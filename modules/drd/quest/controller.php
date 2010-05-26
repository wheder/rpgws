<?php
require_once ('DrD_Dispatcher.php');
require_once ('..\..\..\core\controllerinterface.php');

/**
 * @author Michal Hynèica
 * @version 1.0
 * @created 25-V-2010 16:46:15
 */
class DrD_Quest_Controller implements ControllerInterface
{

	public $m_DrD_Dispatcher;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	public function add_post_action()
	{
	}

	public function create_action()
	{
	}

	public function create_form_action()
	{
	}

	/**
	 * 
	 * @param req
	 */
	public function registerRequest(Request $req)
	{
	}

	/**
	 * 
	 * @param view
	 */
	public function registerView(View $view)
	{
	}

	public function view_action()
	{
	}

}
?>