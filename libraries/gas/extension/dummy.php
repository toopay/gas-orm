<?php namespace Gas\Extension;

/**
 * CodeIgniter Gas ORM Packages
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This packages intend to use as semi-native ORM for CI, 
 * based on the ActiveRecord pattern. This ORM uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas ORM
 * @category    ORM
 * @version     2.1.2
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD
 *
 * =================================================================================================
 * =================================================================================================
 * Copyright 2011 Taufan Aditya a.k.a toopay. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY Taufan Aditya a.k.a toopay ‘’AS IS’’ AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Taufan Aditya a.k.a toopay OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of Taufan Aditya a.k.a toopay.
 * =================================================================================================
 * =================================================================================================
 */

/**
 * Gas\Extension\Dummy Class.
 *
 * @package     Gas ORM
 * @version     2.0.0
 */

use \Gas\Extension;

class Dummy implements Extension {

	/**
	 * @var mixed Gas instance(s)
	 */
	public $gas;
	
	/**
	 * Extension initialization method
	 * 
	 * @param  object
	 * @return void
	 */
	function __init($gas)
	{
		// Here, Gas will transport your instance
		$this->gas = $gas;

		return $this;
	}

	/**
	 * Simple example of Extension usage
	 *
	 * @param  string Argument
	 * @return string Explanation
	 */
	public function explain($arg = NULL)
	{
		// Load CI libraries
		$CI =& get_instance();

		if ( ! class_exists('CI_Typography')) $CI->load->library('typography');

		if ( ! class_exists('CI_Table')) $CI->load->library('table');

		// Build the Extension information
		$argument = var_export($arg, TRUE);
		$fullname = __CLASS__;
		$fragment = explode('\\', $fullname);
		$nickname = strtolower(end($fragment));
		$path     = __FILE__;

		// Determine the caller condition
		if (is_object($this->gas))
		{
			// This for single Gas instance
			$model         = ucfirst($this->gas->model());
			$structure     = $this->gas->meta->get('collumns');
			$relationships = array_keys($this->gas->meta->get('entities'));

			// Get all the records and start build the record table
			$records = $this->gas->record->get();
			$CI->table->set_heading($structure);

			foreach ($records as $record)
			{
				$CI->table->add_row(array_values($record));
			}

			$table = $CI->table->generate();
			$CI->table->clear();
		}
		elseif (is_array($this->gas) && ! empty($this->gas))
		{
			// This for a collection of Gas instance(s)
			$sample        = $this->gas;
			$gas           = array_shift($sample);
			$model         = ucfirst($gas->model());
			$structure     = $gas->meta->get('collumns');
			$relationships = array_keys($gas->meta->get('entities'));

			// Get all the records and start build the record table
			$records = $this->gas;
			$CI->table->set_heading($structure);

			foreach ($records as $record)
			{
				$CI->table->add_row(array_values($record->record->get('data')));
			}

			$table = $CI->table->generate();
			$CI->table->clear();
		}
		else
		{
			// This for nothing
			$model         = 'NULL';
			$structure     = array('undefined');
			$relationships = array('undefined');
			$records       = array();
			$table         = '<strong>Empty Record</strong>'."\n";
		}
		
		// Now build the explanation
		$explanation  = 'Hello, i am an extension. ';
		$explanation .= 'My nickname is <strong>'.$nickname.'</strong> and my fullname is <strong>'.$fullname.'</strong>.';
		$explanation .= "\n";
		$explanation .= 'You can found me at '.$path.'.'."\n";
		$explanation .= 'You call me through '.$model.' instance, and passing bellow arguments : '."\n";
		$explanation .= $argument ."\n";
		$explanation .= 'to processed, and to explain what '.$model.' model looks like.'."\n";
		$explanation .= $model.' have table structure : ';
		$explanation .= implode(', ', $structure)."\n";
		$explanation .= $model.' have defined relationships : ';
		$explanation .= implode(', ', $relationships)."\n";
		$explanation .= $model.' instance now is holding : '.count($records).' record(s)'."\n";
		$explanation .= 'With little help from CodeIgniter\'s Table and Typography library,'."\n";
		$explanation .= 'I can create this paragraph, also output the record into this table : '."\n";
		$explanation .= $table;
		$explanation .= 'So basicly, my purpose is to become a standard interface which you can use,'."\n";
		$explanation .= 'to share common function which utilize either CI Library or your own library, '."\n";
		$explanation .= 'across your Gas models/instances.'."\n";
		$explanation .= 'This is all I can say.'."\n";

		// Format the explanation, then output it
		$formatted_explanation = $CI->typography->auto_typography($explanation);

		return '<pre>'.$formatted_explanation.'</pre>';
	}
}