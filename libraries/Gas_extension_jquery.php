<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Gas Extension jQuery.
 *
 * @package     Gas Library
 * @subpackage	Gas Extension
 * @category    Libraries
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

class Gas_extension_jquery implements Gas_extension {

	public $gas;

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
		$this->gas = $gas;
	}

	/**
	 * get_datatable_input
	 * 
	 * Populate data for jQuery datatable (http://datatables.net)
	 * 
	 * @access public
	 * @param  array
	 * @return array
	 */
	public function get_datatable_input($data)
	{
		// Define all possible input
		$echo = isset($data['sEcho']) ? intval($data['sEcho']) : 1;

		$like = (isset($data['sSearch']) and ! empty($data['sSearch'])) ? $data['sSearch'] : FALSE;

		$limit = (isset($data['iDisplayLength']) and $data['iDisplayLength'] != '-1') ? $data['iDisplayLength'] : 10;

		$offset = (isset($data['iDisplayStart'])) ? $data['iDisplayStart'] : 0;

		$order = (isset($data['iSortCol_0']) and ($data['iSortCol_0'])) ? array($data['iSortCol_0'], $data['sSortDir_0']) : FALSE;

		return array($echo, $like, $limit, $offset, $order);
	}

	/**
	 * set_datatable_output
	 * 
	 * Output JSON for datatable (http://datatables.net)
	 * 
	 * @access public
	 * @param  array
	 * @return json
	 */
	public function set_datatable_output($echo, $limit, $total, $results)
	{
		// Prepare JSON
		$json['sEcho'] = $echo;

		$json['iTotalRecords'] = (string) $limit;

		$json['iTotalDisplayRecords'] = (string) $total;

		foreach($results as $record) $json['aaData'][] = array_values($record->to_array());

		return Gas_janitor::to_json($json);
	}

	/**
	 * datatable
	 * 
	 * Generate JSON for jQuery datatable (http://datatables.net)
	 * 
	 * @access public
	 * @param  array
	 * @return json
	 */
	public function datatable($data)
	{
		list($echo, $like, $limit, $offset, $order) = $this->get_datatable_input($data);
		
		// Working on it
		$model = Gas::factory($this->gas->model());

		$collumns = Gas::factory($this->gas->model())->list_fields();

		if ($like !== FALSE)
		{
			$counter = 0;

			foreach ($collumns as $collumn)
			{
				if ($counter === 0)
				{
					$model->like($collumn, $like);
				}
				else
				{
					$model->or_like($collumn, $like);
				}

				$counter++;
			}
		}

		if ($order !== FALSE)
		{
			$field = array_shift($order);

			$direction = array_shift($order);

			$model->order_by($collumns[$field], $direction);
		}
		
		$model->limit($limit, $offset);

		// Populate result
		$results = $model->all();

		$total = Gas::factory($this->gas->model())->count_all();

		return $this->set_datatable_output($echo, $limit, $total, $results);
	}
}