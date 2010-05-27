<?php
//require_once ('DrD_Dispatcher.php');

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 25-V-2010 16:46:47
 */
class DrD_Character_Controller implements ControllerInterface
{

    public $m_DrD_Dispatcher;
    private $m_view;
    private $m_request;

    function __construct()
    {
    }

    function __destruct()
    {
    }



    public function create_action()
    {
        
    }

    public function create_form_action()
    {
        global $rpgws_config;
        $query = "
                SELECT
                    drd_classes_id as drd_class_id, name
                FROM
                    " . $rpgws_config['db']['prefix'] . "drd_classes
                ORDER BY
                    name ASC
        ";
        $this->m_view->classes = DB::get()->query($query);
        $query = "
                SELECT
                    drd_races_id, name
                FROM
                    " . $rpgws_config['db']['prefix'] . "drd_races
                ORDER BY
                    name ASC
        ";
        $this->m_view->races = DB::get()->query($query);
        
        $this->m_view->printPage();
    }

    public function modify_action()
    {
        
    }

    public function modify_form_action()
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

    public function view_action()
    {
        $this->m_view->err = false;
        try {
            $characters = DrD_Character_Model::load_by_name($this->m_request->get_uri_string());
            $this->m_view->characters = Array();
            //class race description name
            for ($i = 0; $i < sizeof($characters); $i++) {
                $this->m_view->characters[$i]['name'] = $characters[$i]->getname();
                $this->m_view->characters[$i]['class'] = $characters[$i]->getclass();
                $this->m_view->characters[$i]['race'] = $characters[$i]->getrace();
                $this->m_view->characters[$i]['description'] = $characters[$i]->getdescription();
            }
        }
        catch (Exceptions $e) {
            $this->m_view->err = true;
            $this->m_view->mess = $e->get_info();
        }
        $this->m_view->printPage();
    }

    public function index_action()
    {
        $characters = DrD_Character_Model::get_all_names();
        $this->m_view->characters = Array();
        for ($i = 0; $i < sizeof($characters); $i++) {
            $this->m_view->characters[$i] = $characters[$i];
        }
        $this->m_view->printPage();
    }

}
?>