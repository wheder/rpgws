<?php

class DrD_Menu extends Menu {
    public function get_module_menu()
    {
        $result = array();
        $result['Questy'] = '/drd/quest/list';
        $result['Nový quest'] = '/drd/quest/create_form';
        $result['Vaše postavy'] = '/drd/character/list';
        $result['Nová postava'] = '/drd/character/create_form';
        $result['Všechny postavy'] = '/drd/character/index';
        
        return $result;
    }
}
?>