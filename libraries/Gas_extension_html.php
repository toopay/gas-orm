<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Gas Extension HTML.
 *
 * @package     Gas Library
 * @subpackage	Gas Extension
 * @category    Libraries
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

class Gas_extension_html implements Gas_extension {

	public $gas;

	public $table_headings;

	public $table_template;

	public $table_records;

	public $form_labels;

	public $form_definitions;

	public $form_separator = '';

	public $form_entity_prefix = '<p>';

	public $form_entity_suffix = '</p>';

	public $form_submit = '';

	public $hidden_keys = array();

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
	 * hide
	 * 
	 * Hide keys for next operation 
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function hide($keys = array())
	{
		if ( ! empty($keys)) $this->hidden_keys = $keys;

		return $this;
	}

	/**
	 * template
	 * 
	 * Set table templates
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function template($tmpl = array())
	{
		$this->table_template = $tmpl;

		return $this;
	}

	/**
	 * heading
	 * 
	 * Set table heading
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function heading($headings = null)
	{
		if (is_null($headings))
		{
			$list_fields = $this->all_collumns();

			$headings = array_filter($list_fields, get_class($this).'::avoid_keys');

			array_walk_recursive($headings, get_class($this).'::human_name');
		}

		$this->table_headings = $headings;
		
		return $this;
	}

	/**
	 * table
	 * 
	 * Generate HTML table
	 * 
	 * @access public
	 * @return string
	 */
	public function table()
	{
		$CI =& get_instance();

		if ( ! class_exists('CI_Table')) $CI->load->library('table');

		if (is_null($this->table_headings)) $this->heading();

		$CI->table->set_heading($this->table_headings);

		$CI->table->set_template($this->table_template);

		$records = $this->gas->get_raw_record();

		foreach ($records as $record)
		{
			$CI->table->add_row($record);
		}
		
		$raw_rows = $CI->table->rows;

		$CI->table->rows = array();

		foreach ($raw_rows as $raw_row)
		{
			$row = array_shift($raw_row);

			if ( ! empty($this->hidden_keys))
			{
				foreach ($this->hidden_keys as $hidden_key)
				{
					if (isset($row[$hidden_key])) unset($row[$hidden_key]);
				}
			}

			$CI->table->add_row(array_values($row));
		}

		$table = $CI->table->generate();
		
		$CI->table->clear();

		return $table;
	}

	/**
	 * definition
	 * 
	 * Set form fields entity definition
	 * 
	 * @access public
	 * @param  array
	 * @return void
	 */
	public function definition($args = array())
	{
		$this->validate_form();

		$collumns = $this->all_collumns();

		if (empty($args)) $args = $collumns;

		$form_entities = array();

		$records = $this->gas->get_raw_record();

		if (empty($records)) $records[] = array_combine($collumns, array_fill(0, count($collumns), ''));

		$custom = FALSE;

		foreach ($records as $record)
		{
			foreach ($args as $key => $arg)
			{
				if (is_numeric($key))
				{
					$value = isset($record[$arg]) ? $record[$arg] : '';

					if (isset(Gas_core::$old_input[$arg])) $value = Gas_core::$old_input[$arg];

					$option = array('name' => $arg, 'value' => $value);

					$form_entities[$arg] = call_user_func('form_input', $option);
				}
				else
				{
					if ($custom == FALSE)
					{
						$this->definition();

						$custom = TRUE;
					}

					$type = key($arg);

					$value = isset($record[$key]) ? $record[$key] : '';

					if (isset(Gas_core::$old_input[$key])) $value = Gas_core::$old_input[$key];

					$option = $arg[$type];

					if ($type == 'dropdown' or $type == 'multiselect')
					{
						if ($type == 'dropdown')
						{
							$form_entities[$key] = form_dropdown($key, $option, $value);
						}
						else
						{
							$form_entities[$key] = form_multiselect($key.'[]', $option, $value);
						}
					}
					elseif ($type == 'checkbox' or $type == 'radio')
					{
						$checked = ! empty($value);

						$option = array_merge($option, array('name' => $key, 'checked' => $checked));

						$form_entities[$key] = call_user_func('form_'.$type, $option);
					}
					else
					{
						$option = array_merge($option, array('name' => $key, 'value' => $value));

						if ($type == 'hidden')
						{
							$form_entities[$key] = ':hidden:'.call_user_func('form_'.$type, $option);
						}
						else
						{
							$form_entities[$key] = call_user_func('form_'.$type, $option);
						}
					}
				}
			}

			if ($custom == TRUE)
			{
				$this->form_definitions = array_merge($this->form_definitions, $form_entities);
			}
			else
			{
				$this->form_definitions = $form_entities;
			}

			return $this;
		}
	}

	/**
	 * separator
	 * 
	 * Set form separator
	 * 
	 * @access public
	 * @param  string
	 * @return void
	 */
	public function separator($separator = '')
	{
		$this->form_separator = $separator;

		return $this;
	}

	/**
	 * entity
	 * 
	 * Set Entity prefix or/and suffix
	 * 
	 * @access public
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function entity($prefix = '<p>', $suffix = '</p>')
	{
		$this->form_entity_prefix = $prefix;

		$this->form_entity_suffix = $suffix;

		return $this;
	}

	/**
	 * submit
	 * 
	 * Set form submit
	 * 
	 * @access public
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function submit($arg = 'submit', $content = 'Submit')
	{
		$this->validate_form();

		$this->form_submit = is_array($arg) ? form_submit($arg) : form_submit($arg, $content);

		return $this;
	}

	/**
	 * form
	 * 
	 * Generate HTML form
	 * 
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  bool
	 * @param  bool
	 * @return string
	 */
	public function form($url = '', $attributes = array(), $multipart = FALSE, $as_array = FALSE)
	{
		$this->validate_form();

		if (is_null($this->form_definitions)) $this->definition();

		$method = array('method' => 'POST');

		$attributes = empty($attributes) ? $method : array_merge($attributes, $method);

		$open_tag = ($multipart) ? form_open_multipart($url, $attributes) : form_open($url, $attributes);

		$entities = array();

		foreach ($this->form_definitions as $label => $entity)
		{
			if ( ! in_array($label, $this->hidden_keys))
			{
				if (strpos($entity, ':hidden:') === 0)
				{
					$entity = str_replace(':hidden:', '', $entity);

					$entities[] = $this->form_entity_prefix.$entity.$this->form_entity_suffix;
				}
				else
				{
					$entities[] = $this->form_entity_prefix.form_label(ucfirst($label)).$entity.$this->form_entity_suffix;
				}
			}
		}

		if (empty($this->form_submit)) $this->submit();

		$close_tag = form_close();

		if ($as_array)
		{
			$form = array(

				'open_tag' => $open_tag,

				'entities' => $entities,

				'submit' => $this->form_submit,

				'close_tag' => $close_tag,

			);
		}
		else
		{
			$form = $open_tag

				.implode($this->form_separator, $entities)

				.$this->form_separator

				.$this->form_submit

				.$close_tag;
		}

		return $form;
	}

	/**
	 * validate_form
	 * 
	 * Check form helper state
	 * 
	 * @access protected
	 * @return void
	 */
	protected function validate_form()
	{
		if ( ! function_exists('form_open'))
		{
			$CI =& get_instance();

			$CI->load->helper('form');
		}

		return;
	}

	/**
	 * all_collumns
	 * 
	 * Return table collumns name
	 * 
	 * @access protected
	 * @return array
	 */
	protected function all_collumns()
	{
		return $this->gas->list_fields();
	}

	/**
	 * human_name
	 * 
	 * Generate label portion
	 * 
	 * @access protected
	 * @param  mixed
	 * @param  mixed
	 * @return void
	 */
	protected function human_name(&$v, $k)
	{
		$v = ucfirst(str_replace('_', '', $v));
	}

	/**
	 * avoid_keys
	 * 
	 * Sorting keys for hidden keys
	 * 
	 * @access protected
	 * @param  mixed
	 * @param  mixed
	 * @return void
	 */
	protected function avoid_keys($v)
	{
		return( ! in_array($v, $this->hidden_keys));
	}
}