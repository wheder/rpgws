<?php
require_once ('..\..\..\core\controllerinterface.php');
require_once ('DrD_Dispatcher.php');

/**
 * @author Michal Hynèica
 * @version 1.0
 * @created 25-V-2010 16:46:47
 */
class DrD_Character_Controller implements ControllerInterface
{

	public $m_DrD_Dispatcher;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	public function create_action()
	{
	}

	public function create_form_action()
	{
	}

	public function modify_action()
	{
	}

	public function modify_form_action()
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