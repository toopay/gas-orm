<?php defined('CI_APPPATH') or die('No access allowed except via Gas CLI');

/**
 * CodeIgniter Gas ORM Console package
 *
 * CLI package for Gas ORM.
 *
 * @package     Gas Library
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Linguist Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Console
 */

class Gas_linguist {

	public $lines;

	/**
	 * Constructor
	 */
	function __construct()
	{
		require_once(CI_APPPATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'english'.DIRECTORY_SEPARATOR.'gas_lang.php');
		
		$this->lines = $lang;
	}

	/**
	 * line
	 * 
	 * Return a lang line
	 * 
	 * @access public
	 * @param  string  language key
	 * @return mixed
	 */
	public function line($key)
	{
		return ( ! isset($this->lines[$key])) ? FALSE : $this->lines[$key];
	}

}