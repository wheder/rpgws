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
    }



    /**
     * Metoda se postara o zavolani spravneho controlleru
     * @param request
     * @return void     
     */
    public function dispatch(Request $request)
    {
        //TODO: create instance of module dispatcher if modul exists
        
        $this->registerView(new View());
        $module = $request->get_module();
        if(!empty($module) && file_exists(RPGWS_MODULES_PATH . "/$module")) {
            $dispatcher_class = ucfirst($module) . "_Dispatcher";
            $m_ModulDispatcher = new $dispatcher_class();
            $m_ModulDispatcher->registerView($this->m_View);
            $m_ModulDispatcher->dispatch($request);
        } else {
            global $rpgws_config;
            $this->m_View->module = $module;
            $this->m_View->controller = $request->get_controller();
            $this->m_View->action = $request->get_action();  
            $this->m_View->set_layout(RPGWS_LAYOUT_PATH . "/" . $rpgws_config['layout']['default']);
            $this->m_View->set_content(RPGWS_LAYOUT_PATH . "/../view/default.php");
            $this->m_View->printPage();
        }
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