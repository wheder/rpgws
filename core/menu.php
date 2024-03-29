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
	    $auth = new Authentificator();
	    $logged = $auth->logged_user();
	    $result = array();
	    $result['Home'] = '/';
	    $result['Seznam Hráčů'] = '/user/info/show_list';
	    if($logged < 1) {
                $result['Registrace'] = '/user/registration/show_form';
	        $result['Login'] = '/user/login/form';
	    } else {
	        $result['Upravit údaje'] = '/user/info/edit_form';
                $result['Logout'] = '/user/login/logout';
                $result['Dračí doupě'] = '/drd';
	    }
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
