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
 * Gas Command Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Console
 */

class Gas_command {
	
	public $argument = FALSE;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$arg = func_get_args();

		if (count($arg) != 2)
		{
			show_error('No arguments.');

			return FALSE;
		}

		$this->argument = $this->translate($arg[0], $arg[1]);
	}

	/**
	 * translate
	 * 
	 * Translate the input into command
	 * 
	 * @access public
	 * @param  string
	 * @param  string
	 * @return mixed
	 */
	public function translate($type, $command)
	{
		$argument = FALSE;

		switch ($type)
		{
			case 'connect':
				
				if (preg_match('/([^)]+)\((.*?)\)/', $command, $m) and count($m) == 3)
				{
					$sanitize = Gas_type::generate('string', $m[2]);

					$argument = is_null($sanitize)  ? FALSE : $sanitize;
				}

				break;

			case 'query':

				if (preg_match('/([^)]+)\((.*?)\)/', $command, $m) and count($m) == 3 and $query = $m[2])
				{
					$sanitize = Gas_type::generate('string', $m[2]);	

					$argument = is_null($sanitize)  ? FALSE : $sanitize;
				}

				break;

			case 'factory':

				if (preg_match('/([^\n]+)\((.*?)\)/', $command, $m) and count($m) == 3)
				{
					$raw_commands = str_replace('gas::', '', strtolower($m[0]));

					$commands = explode('->', $raw_commands);

					$argument = $commands;
				}

				break;

			case 'method':

				if (preg_match('/([^\n]+)\((.*?)\)/', $command, $m) and count($m) == 3)
				{
					$raw_arguments = $m[2];

					$valid_type = array('array', 'array-string', 'array-int', 'string', 'int', 'empty');

					foreach ($valid_type as $type)
					{
						$sanitize = Gas_type::generate($type, $raw_arguments);

						if ( ! is_null($sanitize))
						{
							$argument = array($m[1] => $sanitize);

							continue;
						}
					}
				}

				break;
		}

		return $argument;
	}
}