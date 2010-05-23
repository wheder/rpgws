<?php


/**
 * @author Jakub Holý
 * @version 1.0
 * @created 23-V-2010 1:54:09
 */
class User_Mailer
{
    private $from;
    private $reply;
	function __construct($from, $reply = "")
	{
	    $this->from = $from;
	    if($reply != "") {
	        $this->reply = $reply; 
	    } else {
	        $this->reply = $from;
	    }
	}

	/**
	 * metoda pro odeslani mailu uzivateli
	 * @param User_Model $user
	 * @param string $content
	 * @return void
	 */
	public function sendMail(User_Model $user, $subject, $content)
	{
	    $header = "To: " . $user->mail . "\r\n";
	    $header .= "From: " . $this->from . "\r\n";
	    $header .= "Reply: " . $this->reply . "\r\n";
	    $header .= "X-Mailer: PHP/" . phpversion();
	    
	    mail($user->mail, $subject, $content, $header);
	}

}
?>