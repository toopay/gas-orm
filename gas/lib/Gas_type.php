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
 * Gas Type Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Console
 */

class Gas_type {

	/**
	 * generate
	 * 
	 * Parsing an argument into proper type
	 * 
	 * @access public
	 * @param  string  expected type
	 * @param  string  unsafe input
	 * @return mixed
	 */
	public static function generate($type, $raw_input)
	{
		$input = null;

		switch ($type)
		{
			case 'string':
				
				if (preg_match('/\'([^)]+)\'/', $raw_input, $m) and count($m) == 2 and strpos($raw_input, ',') == FALSE and strpos($raw_input, '=>') == FALSE)
				{
					$input = $m[1];
				}

				break;

			case 'int':

				if (is_numeric($raw_input) or is_int($raw_input))
				{
					$input = (int) $raw_input;
				}

				break;

			case 'array':

				if (($raw_array = explode('=>', $raw_input)) and count($raw_array) > 1)
				{
					$nodes = explode(',', $raw_input);

					$nodes = Gas_janitor::arr_trim($nodes);

					$array = array();

					foreach ($nodes as $node)
					{
						list($key, $value) = Gas_janitor::arr_trim(explode('=>', $node));

						$valid_key = self::generate('string', $key);

						$valid_value = self::generate('string', $value);

						$array[$valid_key] = $valid_value;
					}

					$input = $array;
				}

				break;

			case 'array-string':

				if (($raw_strings = explode(',', $raw_input)) and count($raw_strings) > 1)
				{
					$nodes = explode(',', $raw_input);

					$nodes = Gas_janitor::arr_trim($nodes);

					$array = array();

					if (count($nodes) == 2)
					{
						$key = self::generate('string', $nodes[0]);

						$value = self::generate('string', $nodes[1]);

						if (is_null($key) or is_null($value))
						{
							$array = null;
						}
						else
						{
							$array[$key] = $value;
						}
					}
					else
					{
						$array = null;
					}

					$input = $array;
				}

				break;

			case 'array-int':

				if (($raw_ints = explode(',', $raw_input)) and count($raw_ints) > 1)
				{
					$nodes = explode(',', $raw_input);

					$nodes = Gas_janitor::arr_trim($nodes);

					$array = array();

					foreach ($nodes as $node)
					{
						if (is_numeric($node))
						{
							$array[] = (int) $node;
						}
						else
						{
							$array = null;

							continue;
						}
					}

					$input = $array;
				}

				break;

			case 'empty':

				if (empty($raw_input))
				{
					$input = $raw_input;
				}

				break;
		}

		return $input;
	}
}