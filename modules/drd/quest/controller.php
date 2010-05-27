<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 25-V-2010 16:46:15
 */
class DrD_Quest_Controller implements ControllerInterface
{

    private $m_View;
    private $m_Request;

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
        $authen = new Authentificator();
        $user = $authen->logged_user();
        
        $author = new Authorizator();
        if(!$author->has_right($user, "create quest")) 
        {
            $this->m_View->err = true;
            $this->m_View->msg = "Nemáte právo vytvářet questy.";
            $this->m_View->printPage();
            return;
        }
        
        $quest = new DrD_Quest_Model();
        $quest->description = $this->m_Request->get_param($desc);
        $quest->game_master_id = $user;
        $quest->active = true;
        $quest->save(); 
        
        $this->m_View->err = false;
        $this->m_View->msg = "Quest úspěšně vytvořen.";
        $this->m_View->printPage();
        return;
    }

    public function create_form_action()
    {
        $authen = new Authentificator();
        $user = $authen->logged_user();
        
        $author = new Authorizator();
        if(!$author->has_right($user, "create quest")) 
        {
            $this->m_View->err = true;
            $this->m_View->msg = "Nemáte právo vytvářet questy.";
            $this->m_View->printPage();
            return;
        }
        
        $this->m_View->err = false;
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
     * Metoda nastavi pouzivane View
     * @param view
     */
    public function registerView(View $view)
    {
        $this->m_View = $view;
    }

    public function view_action()
    {
    }

    public function index_action() 
    {
        header('location: /drd/quest/view');  
    }
}
?>