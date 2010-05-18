<?php


/**
 * @author gambler
 * @version 1.0
 * @created 18-V-2010 14:10:39
 */
interface DispatcherInterface
{
	/**
	 * Metoda se postara o zavolani spravneho controlleru
	 * @param request
	 * @return void	 
	 */
	public function dispatch(Request $request);
}
?>