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
        echo "<br>\nDispatcher zavolan! <br>\n";
        
        //$this->registerView(new View());
        $module = $request->get_module();
        if(!empty($module) && file_exists(RPGWS_MODULES_PATH . "/$module")) {
            $dispatcher_class = ucfirst($module) . "_Dispatcher";
            $m_ModulDispatcher = new $dispatcher_class();
            $m_ModulDispatcher->registerView($this->m_View);
            $m_ModulDispatcher->dispatch($request);
        } else {
            echo "Modul: " . $request->get_module() . "<br>\n";
            echo "Controller: " . $request->get_controller() . "<br>\n";
            echo "Action: " . $request->get_action() . "<br>\n";
        }
    }

	/**
	 * Zaregistruje pouzivanou view tridu	 
	 * @param view
	 * @return void	 
	 */
	public function registerView(View $view)
	{
	   $m_View = $view; 
	}

}
?>