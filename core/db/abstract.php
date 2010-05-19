<?php
interface Db_Abstract {
    function __construct();

	public function affected_rows();

	public function last_insert_id();

	public function num_rows();

	/**
	 * 
	 * @param sql
	 */
	public function query(string $sql);
    
    

}


?>