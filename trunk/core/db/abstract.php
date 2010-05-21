<?php
interface Db_Abstract {
    function __construct();

    /**
     * Metoda pro zjisteni poctu ovlivnenych radku
     * 
     * @return int
     */                   
	public function affected_rows();

    /**
     * Metoda pro zjisteni posledniho ID
     * 
     * @return int
     */                   
	public function last_insert_id();

    /**
     * Metoda pro zjisteni poctu vracenych radku
     * 
     * @return int
     */                   
	public function num_rows();

	/**
	 * Metoda pro provedeni dotazu na DB
	 * 	 
	 * @param string sql
	 * @return array	 
	 */
	public function query($sql);
    
    /**
     * Metoda pro obaleni retezce uvozovkami
     * 
     * @param string
     * @return string
     */
    public function quote($str);                      
}


?>