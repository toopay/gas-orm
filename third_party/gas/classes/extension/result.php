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
 * @version     2.1.0
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
 * Gas\Extension\Result Class.
 *
 * @package     Gas ORM
 * @since     	2.0.0
 */

use \Gas\Extension;

class Result implements Extension {

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
	 * Return the Gas instance as array
	 *
	 * @return array
	 */
	public function as_array()
	{
		if (empty($this->gas)) return array();

		return ($this->gas instanceof \Gas\ORM) ? array($this->gas) : $this->gas;
	}

	/**
	 * Return the Gas instance record(s) to array result
	 *
	 * @return array
	 */
	public function to_array()
	{
		if (empty($this->gas)) return array();

		$records_array = array();

		if ($this->gas instanceof \Gas\ORM)
		{
			$records_array[] = $this->gas->record->get('data');
		}
		else
		{
			foreach ($this->gas as $gas) $records_array[] = $gas->record->get('data');
		}

		return $records_array;
	}

	/**
	 * Convert the Gas Instance records into string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return '<pre>'.var_export($this->to_array(), TRUE).'</pre>';
	}

	/**
	 * Overiding method, triggered when try calling inaccesible method
	 *
	 * @param  string  Available method are : to_xml(), to_json(), to_data()
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		if (preg_match('/^to_(xml|json|data)$/', $method, $m) && count($m) == 2)
		{
			$records_array = $this->to_array();

			switch ($m[1])
			{
				case 'data':
					$records_data = new \Gas\Data();
					$records_data->set('data', $records_array);
					break;

				case 'json':
					$records_data = json_encode($records_array);
					break;

				case 'xml':

					if (  ! function_exists('simplexml_load_file'))
					{
						throw new \RuntimeException('[to_xml]Simple XML must be installed');
					}

					$xml_template = "<?xml version='1.0' standalone='yes'?> <result> </result>";
					$result = new \SimpleXMLElement($xml_template);

					foreach ($records_array as $index => $record)
					{
						$$index = $result->addChild($index);

						foreach ($record as $key => $value)
						{
							$$index->addChild($key, $value);
						}
					}

					$records_data = $result->asXML();
					break;
			}

			return $records_data;
		}

		throw new \BadMethodCallException('['.$method.']Unknown method.');
	}
}