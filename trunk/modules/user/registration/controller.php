<?php

/**
 * @author Jakub Holy
 * @version 1.0
 * @created 22-V-2010 12:30:22
 */
class User_Registration_Controller implements ControllerInterface
{

	private $m_View;
	private $m_Request;
    private $config = Array();

	function __construct()
	{
            include dirname(__FILE__) . "/../config/user_conf.php"; 
            $this->config = $user_config;
	}

	function __destruct()
	{
	}



	public function register_action()
	{
	    $nick = $this->m_Request->get_param("nick", $this->config['nick']['maxlength']);
	    $mail = $this->m_Request->get_param("mail", $this->config['mail']['maxlength']);
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
	    
	    if(strlen($mail) < $this->config['mail']['minlength'])
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Email je příliš krátký.";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    if(filter_var($mail, FILTER_VALIDATE_EMAIL) === false)
	    {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Email nemá platný tvar";
	        $this->m_View->printPage();
	        return;
	    }
	    
	    $user = new User_Model();
	    $user->nick = $nick;
	    $user->mail = $mail;
	    $pass = $user->generate_password($this->config['password']['generated_length']);
	    $user->pass = sha1($nick . ":" . $pass);
	    $user->last_ip = $_SERVER['REMOTE_ADDR'];
	    try {
	        $user->save();
	        $this->m_View->msg = "Uživatel $nick byl úspěšně registrován.";
	        $mailer = new User_Mailer($this->config['mailer']['from'], $this->config['mailer']['reply']);
	        $vars = array();
	        $vars['nick'] = $nick;
	        $vars['mail'] = $mail;
	        $vars['pass'] = $pass;
	        $content = View::load_text(dirname(__FILE__) . "/../mail_temps/new_registration.php", $vars);
	        $mailer->sendMail($user, $this->config['mailer']['new_reg_subject'], $content);
	    } catch (Exception $ex) {
	        $this->m_View->err = true;
	        $this->m_View->msg = "Uživatel nebo e-mail již jsou v databázi.";
	    }
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
	 * 
	 * @param view
	 */
	public function registerView(View $view)
	{
	    $this->m_View = $view;
	}

	public function show_form_action()
	{
	    //global $user_config;
	    
            
	    $this->m_View->nick_max = $this->config['nick']['maxlength'];
	    $this->m_View->pass_max = $this->config['password']['maxlength'];
	    $this->m_View->mail_max = $this->config['mail']['maxlength'];
	    $this->m_View->printPage();
	}

	public function index_action()
	{
	    header("location: /user/registration/show_form");
	}
}
?>
