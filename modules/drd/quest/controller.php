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