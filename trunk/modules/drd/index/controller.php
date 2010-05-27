<?php

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 25-V-2010 16:46:47
 */
class DrD_Index_Controller implements ControllerInterface
{

    private $m_view;
    private $m_request;

    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
        * 
        * @param req
        */
    public function registerRequest(Request $req)
    {
        $this->m_request = $req;
    }

    /**
        * 
        * @param view
        */
    public function registerView(View $view)
    {
        $this->m_view = $view;
    }

    public function index_action()
    {
        $this->m_view->printPage();
    }

}
?>
