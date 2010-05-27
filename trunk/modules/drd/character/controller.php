<?php
//require_once ('DrD_Dispatcher.php');

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 25-V-2010 16:46:47
 */
class DrD_Character_Controller implements ControllerInterface
{

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
        $auth = new Authentificator();
        $user = $auth->logged_user();
        
        $name = $this->m_request->get_param('name');
        if(empty($name)) {
            $this->m_view->err = true;
            $this->m_view->msg = "Příliš krátké jméno postavy.";
            $this->m_view->printPage();
            return;
        }
        
        $char = new DrD_Character_Model();
        $char->name = $name;
        $char->mana = $this->m_request->get_param_int('mana');
        $char->hit_points = $this->m_request->get_param_int('hitpoint');
        $char->description = $this->m_request->get_param('description');
        $char->items = $this->m_request->get_param('items');
        $race = DrD_Race_Model::load($this->m_request->get_param_int('race'));
        $class = DrD_Class_Model::load($this->m_request->get_param_int('class'));
        $char->race = $race;
        $char->class = $class;
     	$char->owner = $user;
        $char->save();
        
        $this->m_view->err = false;
        $this->m_view->msg = "Postava byla úspěšně vytvořena";
        $this->m_view->printPage();
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
    global $rpgws_config;
        $auth = new Authentificator();
        $user = $auth->logged_user();
        
        $id = $this->m_request->get_uri_id();
        if(empty($id)) header('location: /drd/character/list');
        
        $char = DrD_Character_Model::load($id);
	
        //nacte questy daneho GM
        $db = Db::get();
        $on_gms_quest = false;
        $query = "
            SELECT
                drd_quest_id
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
            WHERE
                game_master_id = " . $db->quote($user) ."
        ";
        $result = $db->query($query);
        
        if(!empty($result)) foreach($result as $row) {
            if($char->is_in_quest($row['drd_quest_id'])) {
                $on_gms_quest = true;
                break;
            }
        }
        
        if($char->owner != $user && !$on_gms_quest)
        {
            $this->m_view->err = true;
            $this->m_view->msg = "Nemáte právo upravovat tuto postavu.";
            $this->m_view->printPage();
            return;
        }
        
        $char->name = $this->m_request->get_param_int('name');
        $char->mana = $this->m_request->get_param_int('mana');
        $char->hit_points = $this->m_request->get_param_int('hitpoint');
        $char->description = $this->m_request->get_param('description');
        $char->items = $this->m_request->get_param('items');
        $char->save();
        
        $this->m_view->err = false;
        $this->m_view->msg = "Postava byla úspěšně uložena.";
        $this->m_view->printPage();
    }

    public function modify_form_action()
    {
        global $rpgws_config;
        $auth = new Authentificator();
        $user = $auth->logged_user();
        
        $nick = $this->m_request->get_uri_string();
        if(empty($nick)) header('location: /drd/character/list');
        
        $char = DrD_Character_Model::load_by_name($nick);
	    $char = $char[0];
        //nacte questy daneho GM
        $db = Db::get();
        $on_gms_quest = false;
        $query = "
            SELECT
                drd_quest_id
            FROM
                " . $rpgws_config['db']['prefix'] . "drd_quests
            WHERE
                game_master_id = " . $db->quote($user) ."
        ";
        $result = $db->query($query);
        
        if(!empty($result)) foreach($result as $row) {
            if($char->is_in_quest($row['drd_quest_id'])) {
                $on_gms_quest = true;
                break;
            }
        }
        
        if($char->owner != $user && !$on_gms_quest)
        {
            $this->m_view->err = true;
            $this->m_view->msg = "Nemáte právo upravovat tuto postavu.";
            $this->m_view->printPage();
            return;
        }
        
	    $this->m_view->err = false;
        $this->m_view->char = $char;
        $this->m_view->printPage();
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

    public function list_action()
    {
        $auth = new Authentificator();
        $user = $auth->logged_user();
        $characters = DrD_Character_Model::load_by_player($user);
        
        $this->m_view->characters = $characters;
        $this->m_view->printPage();
    }
    
    public function index_action()
    {
        $characters = DrD_Character_Model::get_all_names();
        $this->m_view->characters = Array();
        //wtf?
        /*for ($i = 0; $i < sizeof($characters); $i++) {
            $this->m_view->characters[$i] = $characters[$i];
        }*/
        $this->m_view->characters = $characters;
        $this->m_view->printPage();
    }

}
?>
