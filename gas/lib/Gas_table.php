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
 * Gas Table Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Console
 */

class Gas_table {

	protected $_headers = array();

	protected $_width = array();

	protected $_rows = array();

	/**
	 * display
	 * 
	 * Output the table
	 * 
	 * @access public
	 * @return string
	 */
	public function display() 
	{
		$border = '+';

		foreach ($this->_headers as $column => $header) 
		{
			$border .= '-' .str_repeat('-', $this->_width[$column]).'-+';
		}

		Gas_CLI::line($border);

		Gas_CLI::line($this->render_row($this->_headers));

		Gas_CLI::line($border);

		foreach ($this->_rows as $row) 
		{
			Gas_CLI::line($this->render_row($row));
		}

		Gas_CLI::line($border);
	}

	/**
	 * set_headers
	 * 
	 * Generate table headers
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function set_headers($headers = array()) 
	{
		$this->_headers = $this->check_row($headers);

		return $this;
	}

	/**
	 * add_row
	 * 
	 * Building the row
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function add_row($row = array()) 
	{
		$this->_rows[] = $this->check_row($row);

		return $this;
	}

	/**
	 * set_row
	 * 
	 * Generate row portion
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function set_rows($rows = array()) 
	{
		$this->_rows = array();
		
		foreach ($rows as $row)  $this->add_row($row);

		return $this;
	}

	/**
	 * check_row
	 * 
	 * Validate row
	 * 
	 * @access protected
	 * @param  array
	 * @return array
	 */
	protected function check_row($row = array()) 
	{
		foreach ($row as $column => $str) 
		{
			$width = strlen($str);

			if ( ! isset($this->_width[$column]) or $width > $this->_width[$column]) 
			{
				$this->_width[$column] = $width;
			}
		}

		return $row;
	}

	/**
	 * render_row
	 * 
	 * Generate constranit portion
	 * 
	 * @access protected
	 * @param  array
	 * @return string
	 */
	protected function render_row($row = array()) 
	{
		$render = '|';

		foreach ($row as $column => $val) 
		{
			$render .= ' '.str_pad($val, $this->_width[$column]).' |';
		}

		return $render;
	}
}