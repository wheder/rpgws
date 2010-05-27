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
        
        $quest = new DrD_Quest_Model();
        $quest->description = $this->m_Request->get_param('desc');
        if(empty($quest->description)) {
            $this->m_View->err = true;
            $this->m_View->msg = "Prázdný popisek questu.";
            $this->m_View->printPage();
            return;
        }
        
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
        $auth = new Authentificator();
        $user = $auth->logged_user();
        
        $id = $this->m_Request->get_uri_id();

        if(empty($id)) header('location: /drd/quest/list');
        
        $quest = DrD_Quest_Model::load($id);
        $this->m_View->chars = DrD_Character_Model::load_by_quest($quest->quest_id);
        
        $at_quest = false;
        if(!empty($this->m_View->chars))
        {
            foreach($this->m_View->chars as $char) {
                if($char->owner == $user) {
                    $at_quest = true;
                    break;
                }
            }
        }
        
        if(!$at_quest && $user != $quest->game_master_id) {
            $this->m_View->err = true;
            $this->m_View->msg = "Nemáte žádnou postavu v tomto questu";
        }
        
        $this->m_View->quest = $quest;
        $posts = DrD_Quest_Post_Model::load_all_by_quest($quest->quest_id);
        $this->m_View->posts = array();
        
        foreach ($posts as $post) {
            if($post->is_whisper_to($user) || $quest->game_master_id == $user) {
                array_push($this->m_View->posts, $post);
            }
        }
        
        
        
        $this->m_View->printPage();
    }
    
    public function list_action()
    {
        
    }

    public function index_action() 
    {
        header('location: /drd/quest/list');  
    }
}
?>
