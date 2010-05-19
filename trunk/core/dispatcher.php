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
        
        
        $this->registerView(new View());
        $module = $request->get_module();
        if(!empty($module) && file_exists(RPGWS_MODULES_PATH . "/$module")) {
            $dispatcher_class = $module . "_dispatcher";
            $m_ModulDispatcher = new $dispatcher_class();
            $m_ModulDispatcher->registerView($this->m_View);
            $m_ModulDispatcher->dispatch($request);
        } else {
            global $rpgws_config;
            $this->m_View->ecode = 404;
            $this->m_View->error = "Page not found.";
            $this->m_View->emsg = "Requested module doesn't exist.";
            $this->m_View->module = $module;
            
            $this->m_View->set_layout(RPGWS_LAYOUT_PATH . "/" . $rpgws_config['layout']['default']);
            $this->m_View->set_content(RPGWS_VIEW_PATH . "/" . $rpgws_config['view']['error']);
             
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