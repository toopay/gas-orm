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
 * Gas\Extension\Jquery Class.
 *
 * @package     Gas ORM
 * @since     	2.0.0
 */

use \Gas\Extension;

class Jquery implements Extension {

	/**
	 * @var mixed Gas instance(s)
	 */
	public $gas;

	/**
	 * @var string Fields selector
	 */
	public $selector;
	
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
	 * Set selector
	 *
	 * @param  string	Fields to be included
	 * @return void
	 */
	public function set_select($fields = '')
	{
		$this->selector = $fields;

		return $this;
	}

	/**
	 * Populate data for jQuery datatable (http://datatables.net)
	 * 
	 * @param  array
	 * @return array
	 */
	public function get_datatable_input($data)
	{
		// Define all possible input initial value
		$echo   = 1;
		$like   = FALSE;
		$limit  = 10;
		$offset = 0;
		$order  = FALSE;

		// Define needed key, and itterate over the data
		$keys = array('sEcho', 'sSearch', 'iDisplayLength', 'iDisplayStart', 'iSortCol_0');

		foreach ($keys as $key)
		{
			if (array_key_exists($key, $data))
			{
				// What to do if the key exists
				switch ($key)
				{
					case 'sEcho':
						$echo = intval($data['sEcho']);

						break;

					case 'sSearch':
						if ( ! empty($data['sSearch']))
						{
							$like = $data['sSearch'];
						}

						break;
					
					case 'iDisplayLength':
						if ($data['iDisplayLength'] != '-1')
						{
							$limit = $data['iDisplayLength'];
						}

						break;

					case 'iDisplayStart':
						$offset = $data['iDisplayStart'];

						break;

					case 'iSortCol_0':
						$order = array($data['iSortCol_0'], $data['sSortDir_0']);

						break;
				}
				
			}
		}

		return array($echo, $like, $limit, $offset, $order);
	}

	/**
	 * Output JSON for datatable (http://datatables.net)
	 * 
	 * @param  array
	 * @return json
	 */
	public function set_datatable_output($echo, $limit, $total, $results)
	{
		// Prepare JSON
		$json['sEcho']                = $echo;
		$json['iTotalRecords']        = (string) $limit;
		$json['iTotalDisplayRecords'] = (string) $total;

		// Build the records
		if ( ! empty($results))
		{
			foreach($results as $record) 
			{
				// Get the record
				$record = array_values($record->record->get('data'));
				$json['aaData'][] = $record;
			}
		}
		else
		{
			$json['aaData'] = array();
		}

		// Output the json
		return json_encode($json);
	}

	/**
	 * Generate JSON for jQuery datatable (http://datatables.net)
	 * 
	 * @param  array
	 * @return json
	 */
	public function datatable($data)
	{
		list($echo, $like, $limit, $offset, $order) = $this->get_datatable_input($data);

		// Validate the gas state
		if (is_object($this->gas))
		{
			$gas = $this->gas;
		}
		elseif (is_array($this->gas))
		{
			$sample = $this->gas;
			$gas    = array_shift($sample);
		}

		// Working on it
		$model    = $gas;
		$collumns = $gas->meta->get('collumns');

		// Do we have an active selector ?
		if ( ! empty($this->selector))
		{
			$model->select($this->selector);
		}

		// Think!
		$gesture = array('name', 'title', 'description');
		$behave = array_values(array_intersect($collumns, $gesture));
		sort($behave);

		// Process LIKE clause
		if ($like !== FALSE && ! empty($behave))
		{
			foreach ($behave as $counter => $collumn)
			{
				if ($counter == 0)
				{
					$model->like($collumn, $like);
				}
				else
				{
					$model->or_like($collumn, $like);
				}
			}
		}

		// Process ORDER BY clause
		if (is_array($order))
		{
			$field     = array_shift($order);
			$direction = array_shift($order);
			$model->order_by($collumns[$field], $direction);
		}
		
		// Process LIMIT clause
		$model->limit($limit, $offset);

		// Execute all
		$model->all();

		// Populate the result
		$results = is_object($this->gas) ? array($this->gas) : $this->gas;

		// Get actual total records
		$CI =& get_instance();

		if ( ! empty($results) && ($sample = current($results)))
		{
			$total = $CI->db->count_all_results($sample->table);
		}
		else
		{
			$total = is_object($this->gas) ? 1 : count($this->gas);
		}

		return $this->set_datatable_output($echo, $limit, $total, $results);
	}
}