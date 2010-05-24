<?php

/**
 * @author Jakub Holý
 * @version 1.0
 * @created 22-V-2010 12:30:52
 */
class User_LostPassword_Controller implements ControllerInterface
{

    private $m_View;
    private $m_Request;
    private $config;
    
    function __construct()
    {
        include dirname(__FILE__) . "/../config/user_conf.php"; 
        $this->config = $user_config;
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
        $this->m_Request = $req;
    }

    /**
     * 
     * @param view
     */
    public function registerView(View $view)
    {
        $this->m_View = $view;
    }

    public function send_password_action()
    {
        $nick = $this->m_Request->get_param("nick", $this->config['nick']['maxlength']);
        $mail = $this->m_Request->get_param("mail", $this->config['mail']['maxlength']);
        $day = $this->m_Request->get_param_int("day");
        $month = $this->m_Request->get_param_int("month");
        $year = $this->m_Request->get_param_int("year");
        $date = "$year-$month-$day";
        if(strlen($nick) < $this->config['nick']['minlength']) 
        {
            $this->m_View->err = true;
            $this->m_View->msg = "Uživatelské jméno je příliš krátké.";
            $this->m_View->printPage();
            return;
        }
        
        if($this->config['nick']['regexp']['match_required'] && !preg_match($this->config['nick']['regexp']['content'], $nick))
        {
            $this->m_View->err = true;
            $this->m_View->msg = "Uživatelské jméno obsahuje nepovolené znaky";
            $this->m_View->printPage();
            return;
        }
        
        $user = new User_Model();
        try {
            $user->load(0, $nick);
        } catch (NonExistUserException $ex) {
            $this->m_View->err = true;
            $this->m_View->msg = "Uživatel neexistuje.";
            $this->m_View->printPage();
            return;
        }
        
        if($born != $user->born || $mail != $user->mail)
        {
            $this->m_View->err = true;
            $this->m_View->msg = "Zadané údaje nejsou správné.";
            $this->m_View->printPage();
            return;
        }
        
        $pass = $user->generate_password($this->config['password']['generated_length']);
	    $user->pass = sha1($nick . ":" . $pass);
	    $user->save();
	        
	    $mailer = new User_Mailer($this->config['mailer']['from'], $this->config['mailer']['reply']);
	    $vars = array();
	    $vars['nick'] = $nick;
	    $vars['mail'] = $mail;
	    $vars['pass'] = $pass;
	    $content = View::load_text(dirname(__FILE__) . "/../mail_temps/lost_password.php", $vars);
	    $mailer->sendMail($user, $this->config['mailer']['lost_pass_subject'], $content);
	    
	    $this->m_View->err = false;
	    $this->m_View->msg = "Na váš mail bylo posláno nové heslo.";
	    $this->m_View->printPage();
    }

    public function show_form_action()
    {
        
        $this->nick_max = $this->config['nick']['maxlength'];
        $this->mail_max = $this->config['mail']['maxlength'];
        $this->m_View->printPage();
    }

}
?>