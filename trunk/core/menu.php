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
	    $result['Registrace'] = '/user/registration/show_form';
	    $result['Login'] = '/user/login/form';
	    $result['Logout'] = '/user/login/logout';
	    
	    return $result;
	}

	/**
	 * Vrati cele menu v poli
	 * 
	 * @return array
	 */
	public function get_menu()
	{
	    $result = $this->get_core_menu();
	    $result = array_merge($result, $this->get_module_menu()); 
	    return $result;
	}
	
	/**
	 * metoda pro samostatne menu modulu
	 * 
	 * @return array
	 */
	public function get_module_menu()
	{
	    return array();    
	}
}
?>