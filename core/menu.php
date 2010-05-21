<?php


/**
 * @author gambler
 * @version 1.0
 * @created 21-V-2010 13:20:38
 */
class Menu
{
    /**
     * vrati menu jadra v poli
     * 
     * @return array
     */
	final public function get_core_menu()
	{
	    $result = array();
	    $result['Home'] = '/';
	    $result['Registrace'] = '/user/registration/new';
	    $result['Login'] = '/user/login/in';
	    $result['Logout'] = '/user/login/out';
	    
	    return $result;
	}

	/**
	 * Vrati cele menu v poli
	 * 
	 * @return array
	 */
	public function get_menu()
	{
	    return $this->get_core_menu();
	}
}
?>