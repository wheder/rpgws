<?php


/**
 * @author Jakub Holý
 * @version 1.0
 * @created 22-V-2010 1:14:13
 */
interface ControllerInterface
{

	/**
	 * Zaregistruje instanci tridy request pro pouziti v controlleru
	 * @param Request $req
	 * @return void
	 */
	public function registerRequest(Request $req);

	/**
	 * Zaregistruje instanci tridy view pro pouziti v controlleru
	 * @param View $view
	 * @return void
	 */
	public function registerView(View $view);

	/**
	 * vychozi akce controlleru
	 * @return void
	 */
	public function index_action();
}
?>