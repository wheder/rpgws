<?php
/**
 * @author gambler
 * @version 1.0
 * @created 18-V-2010 14:14:22
 */
class Dispatcher implements DispatcherInterface
{

	public $m_View;
	public $m_ModulDispatcher;

	public function __construct()
	{
	   //TODO: create instance of view
	}



	/**
	 * Metoda se postara o zavolani spravneho controlleru
	 * @param request
	 * @return void	 
	 */
	public function dispatch(Request $request)
	{
	   //TODO: create instance of module dispatcher if modul exists
	   echo "Dispatcher zavolan! <br>\n";
	   
	   echo "Modul: " . $request->get_module() . "<br>\n";
	   echo "Controller" . $request->get_controller() . "<br>\n";
	   echo "Action: " . $request->get_action() . "<br>\n";
	}

}
?>