<?php
/**
 * @author gambler
 * @version 1.0
 * @created 18-V-2010 14:14:22
 */
class Dispatcher implements DispatcherInterface
{

    private $m_View;
    private $m_ModulDispatcher;
    private $m_Request;
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
        
        try {
            $this->m_Request = $request;
            $this->registerView(new View());
            $module = $this->m_Request->get_module();
            if(!empty($module) && file_exists(RPGWS_MODULES_PATH . "/$module")) {
                $dispatcher_class = $module . "_dispatcher";
                $m_ModulDispatcher = new $dispatcher_class();
                $m_ModulDispatcher->registerView($this->m_View);
                $m_ModulDispatcher->dispatch($request);
            } else {
                   throw new NotFoundException("Requested module '$module' doesn't exist.", "Error 404", "Not Found", 404);
            }
        } catch (Exceptions $ex) {
            $this->ShowException($ex);
        } catch (Exception $ex) {
            echo $ex;
            //pripadne nejaky basic exception handler
            exit;
        }
        
    }
    
    /**
     * Metoda pro osetreni vyjimek
     * 
     * @param Exception
     * @return void               
     */              
    private function ShowException(Exception $ex)
    {
            global $rpgws_config;
            $this->m_View->code = $ex->getCode();
            $this->m_View->head = $ex->get_header();
            $this->m_View->message = $ex->get_info();
            $this->m_View->debug_info = $ex->getMessage();
            
            $this->m_View->set_layout(RPGWS_LAYOUT_PATH . "/" . $rpgws_config['layout']['default']);
            $this->m_View->set_content(RPGWS_VIEW_PATH . "/" . $rpgws_config['view']['error']);
             
            $this->m_View->printPage();
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
