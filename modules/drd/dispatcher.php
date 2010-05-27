<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:47:15
 */
class DrD_Dispatcher implements DispatcherInterface
{

    private $m_View;

    function __construct()
    {
        $this->m_View = null;
    }

    function __destruct()
    {
    }



    /**
     * Metoda se postara o zavolani spravneho controlleru
     * @return void
     * 
     * @param Request $request
     */
    public function dispatch(Request $request)
    {
        $auth = new Authentificator();
        $logged = $auth->logged_user();
        
        if($logged < 1) header("location: /");
        $this->add_user($logged);
        
        global $rpgws_config;
        $controller = $request->get_uri_string();
        $action = $request->get_uri_string();
        if(empty($action)) $action = "index";
        
        $cont_class = "DrD_" . $controller . "_Controller";
        $action_method = $action . "_action";
        
        $view_file = dirname(__FILE__) . "/view/" . $controller . "_" . $action . ".php";
        $this->m_View->set_layout(RPGWS_LAYOUT_PATH . "/" . $rpgws_config['layout']['default']);
        $this->m_View->set_content($view_file);
        $this->m_View->set_menu(new DrD_Menu());
        
        $cont = new $cont_class();
        if(!method_exists($cont, $action_method)) $action_method = "index_action";
        $cont->registerView($this->m_View);
        $cont->registerRequest($request);
        $cont->$action_method();
    }
    
    protected function add_user($user_id) 
    {
        global $rpgws_config;
        $db = DB::get();
        $query = "
            INSERT IGNORE INTO
                " . $rpgws_config['db']['prefix'] . "drd_players
                (drd_player_id)
            VALUES (
            	" . $db->quote($user_id) . ") 
        ";     
        
        $db->query($query);
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