<?php

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 22-V-2010 12:31:54
 */
class User_Dispatcher implements DispatcherInterface
{
    private $m_View;
	function __construct()
	{
	    $this->m_Request = null;
	    $this->m_View = null;
	}

	function __destruct()
	{
	}



	/**
	 * Metoda se postara o zavolani spravneho controlleru
	 * @return void
	 * 
	 * @param request
	 */
	public function dispatch(Request $request)
	{
	    $controller = $request->get_uri_string();
	    $action = $request->get_uri_string();
	    $cont_class = "User_" . $controller . "_Controller";
	    $action_method = $action . "_action";
	    
	    $view_file = dirname(__FILE__) . "/view/" . $controller . "_" . $action . ".php";
	    $this->m_View->set_layout(RPGWS_LAYOUT_PATH . "/" . $rpgws_config['layout']['default']);
	    $this->m_View->set_content($view_file);
	    $this->m_View->set_menu(new Menu());
	    
	    $cont = new $cont_class();
	    $cont->registerView($this->m_View);
	    $cont->registerRequest($request);
	    $cont->$action_method();
	}

	/**
	 * Zaregistruje pouzivanou view tridu	 
	 * @param view
	 * @return void	 
	 */
	public function registerView(View $view)
	{
	   $this->m_View = $view; 
	}
}
?>