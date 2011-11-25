<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Gas Extension Dummy.
 *
 * @package     Gas Library
 * @subpackage	Gas Extension
 * @category    Libraries
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

class Gas_extension_dummy implements Gas_extension { 
	
	// This is a properties, which will be used to transport Gas Instance
	public $gas;

	public $explanation;

	/**
	 * __init
	 * 
	 * Initialize extension
	 * 
	 * @access public
	 * @param  object
	 * @return void
	 */
	public function __init($gas)
	{
		// Here, Gas will transport your instance
		$this->gas = $gas;
	}

	/**
	 * explain
	 * 
	 * Explain about related Gas instance
	 * 
	 * @access public
	 * @param  mixed
	 * @return string
	 */
	public function explain($args = null)
	{
		$CI =& get_instance();

		if ( ! class_exists('CI_Typography')) $CI->load->library('typography');

		if ( ! class_exists('CI_Table')) $CI->load->library('table');

		$arguments = ! is_null($this->explanation) ? var_export($this->explanation, TRUE) : var_export($args, TRUE);

		$nickname = key($this->gas->extensions);

		$fullname = $this->gas->extensions[$nickname];

		$path = __FILE__;

		$model = ucfirst($this->gas->model());

		$structure = $this->gas->list_fields();

		$relationships = array_keys($this->gas->relations);

		$records = $this->gas->get_raw_record();

		$CI->table->set_heading($structure);

		foreach ($records as $record)
		{

			$CI->table->add_row(array_values($record));

		}

		$table = $CI->table->generate();

		$CI->table->clear();


		$explanation = 'Hello, i am an extension. ';

		$explanation .= 'My nickname is '.$nickname.' and my fullname is '.$fullname.'.'."\n";

		$explanation .= 'You can found me at '.$path.'.'."\n";

		$explanation .= 'You call me through '.$model.' instance, and passing bellow arguments : '."\n";

		$explanation .= $arguments ."\n";

		$explanation .= 'to processed, and to explain what '.$model.' model looks like.'."\n";

		$explanation .= $model.' model have table structure : ';

		$explanation .= implode(', ', $structure)."\n";

		$explanation .= $model.' model have defined relationship : ';

		$explanation .= implode(', ', $relationships)."\n";

		$explanation .= $model.' instance now is holding : '.count($records).' record(s)'."\n";

		$explanation .= 'With little help from Table and Typography library,'."\n";

		$explanation .= 'I can create this paragraph, also output the record into this table : '."\n";

		$explanation .= $table;

		$explanation .= 'So basicly, my purpose is to become a standard interface which you can use,'."\n";

		$explanation .= 'to share common function which utilize either CI Library or your own library, '."\n";

		$explanation .= 'across your Gas models/instances.'."\n";

		$explanation .= 'This is all I can say.'."\n";

		$formatted_explanation = $CI->typography->auto_typography($explanation);

		return '<pre>'.$formatted_explanation.'</pre>';
	}
}