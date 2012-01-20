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
 * @version     2.0.0
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
 * Gas\Extension\Html Class.
 *
 * @package     Gas ORM
 * @version     2.0.0
 */

use \Gas\Extension;

class Html implements Extension {

	/**
	 * @var mixed Gas instance(s)
	 */
	public $gas;

	/**
	 * @var array Table headings holder
	 */
	public $table_headings;

	/**
	 * @var array Table template holder
	 */
	public $table_template;

	/**
	 * @var array Table records holder
	 */
	public $table_records;

	/**
	 * @var array Form labels holder
	 */
	public $form_labels;

	/**
	 * @var array Form definitions holder
	 */
	public $form_definitions;

	/**
	 * @var string Form separator holder
	 */
	public $form_separator = '';

	/**
	 * @var string Form prefix tag
	 */
	public $form_entity_prefix = '<p>';

	/**
	 * @var string Form suffix tag
	 */
	public $form_entity_suffix = '</p>';

	/**
	 * @var array Form submit url
	 */
	public $form_submit = '';

	/**
	 * @var array Hidden keys holder
	 */
	public $hidden_keys = array();
	
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
	 * Hide keys for next operation 
	 * 
	 * @param  mixed
	 * @return void
	 */
	public function hide($keys)
	{
		// Parse if the argument was string
		if (is_string($keys))
		{
			$keys = explode(',', $keys);
		}

		if ( ! empty($keys)) $this->hidden_keys = $keys;

		return $this;
	}

	/**
	 * Set table templates
	 * 
	 * @param  array
	 * @return void
	 */
	public function template($tmpl = array())
	{
		$this->table_template = $tmpl;

		return $this;
	}

	/**
	 * Set table heading
	 * 
	 * @param  array
	 * @return void
	 */
	public function heading($headings = null)
	{
		if (is_null($headings))
		{
			// Get all collumns name, and filter for avoid keys which declared before final method
			$list_fields = $this->all_collumns();
			$headings    = array_filter($list_fields, get_class($this).'::avoid_keys');
			array_walk_recursive($headings, get_class($this).'::human_name');
		}

		$this->table_headings = $headings;
		
		return $this;
	}

	/**
	 * Generate HTML table
	 * 
	 * @return string
	 */
	public function table()
	{
		$CI =& get_instance();

		if ( ! class_exists('CI_Table')) $CI->load->library('table');

		if (is_null($this->table_headings)) $this->heading();

		$CI->table->set_heading($this->table_headings);
		$CI->table->set_template($this->table_template);

		// Validate the records
		if (is_object($this->gas))
		{
			$records = $this->gas->record->get();
		}
		elseif (is_array($this->gas) && ! empty($this->gas))
		{
			$records = $this->gas;
		}
		else
		{
			$records = array();
		}

		// Fill the table rows
		foreach ($records as $record)
		{
			$record = (is_object($record)) ? $record->record->get('data') : $record;
			$CI->table->add_row($record);
		}
		
		// Do we need to hide some collumn?
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

		// Generate the table
		$table = $CI->table->generate();
		$CI->table->clear();

		return $table;
	}

	/**
	 * Set form fields entity definition
	 * 
	 * @param  array
	 * @return void
	 */
	public function definition($args = array())
	{
		$this->validate_form();
		$collumns = $this->all_collumns();

		if (empty($args)) $args = $collumns;
		
		// Validate the records
		if (is_object($this->gas))
		{
			$gas_records = $this->gas->record->get();

			if (empty($gas_records))
			{
				$records[] = array_combine($collumns, array_fill(0, count($collumns), ''));
			}
			else
			{
				$records = $gas_records;
			}
		}
		elseif (is_array($this->gas) && ! empty($this->gas))
		{
			$records = $this->gas;
		}
		else
		{
			$records[] = array_combine($collumns, array_fill(0, count($collumns), ''));
		}

		// Initial form definition
		$form_entities = array();
		$custom        = FALSE;

		// Loop over the record(s)
		foreach ($records as $record)
		{
			$record = (is_object($record)) ? $record->record->get('data') : $record;

			if ( ! $args)
			{
				// This came from multiple instance, which not allowed (yet)
				throw new \InvalidArgumentException('[definition]Form method(s) can not handle multiple instances');
			}

			foreach ($args as $key => $arg)
			{
				if (is_numeric($key))
				{
					// No custom definition has been set
					$value               = isset($record[$arg]) ? $record[$arg] : '';
					$option              = array('name' => $arg, 'value' => $value);
					$form_entities[$arg] = call_user_func('form_input', $option);
				}
				else
				{
					// Mark the custom flag
					if ($custom == FALSE)
					{
						$this->definition();
						$custom = TRUE;
					}

					// Here we must handle custom form definition(s)
					$type   = key($arg);
					$value  = isset($record[$key]) ? $record[$key] : '';
					$option = $arg[$type];
					$needed = array('dropdown', 'multiselect', 'checkbox', 'radio');

					if ( ! is_array($option) && in_array($type, $needed))
					{
						// Not valid argument for further usage
						throw new \InvalidArgumentException('[definition]'.$type.' should contain valid option');
					}

					// Determine the type of form entity, then generate decent entity definition
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
						$checked             = ! empty($value);
						$option              = array_merge($option, array('name' => $key, 'checked' => $checked));
						$form_entities[$key] = call_user_func('form_'.$type, $option);
					}
					else
					{
						// Determine how we handle the option
						if (is_string($option))
						{
							// This mean, option contain direct value.
							// Now, we need to know whether corresponding value exists or not
							if (empty($value))
							{
								// No related value found, use this option
								$option = array('name' => $key, 'value' => $option);
							}
							else
							{
								// Related value found, overide this option
								$option = array('name' => $key, 'value' => $value);
							}
						}
						elseif (is_array($option))
						{
							// Option already contain both 'name' and 'value', 
							// merge it with our model instance entity
							$option = array_merge($option, array('name' => $key, 'value' => $value));
						}

						if ($type == 'hidden')
						{
							$name = $option['name'];
							$val  = $option['value'];

							$form_entities[':hidden:'.$key] = form_hidden($name, $val);
						}
						else
						{
							if ( ! function_exists('form_'.$type))
							{
								throw new \BadMethodCallException('[definition]Unknown form method within CI Form helper');
								
							}

							$form_entities[$key] = call_user_func('form_'.$type, $option);
						}
					}
				}
			}

			// Finalize the form definitions
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
	 * Set form separator
	 * 
	 * @param  string
	 * @return void
	 */
	public function separator($separator = '')
	{
		$this->form_separator = $separator;

		return $this;
	}

	/**
	 * Set Entity prefix or/and suffix
	 * 
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
	 * Set form submit
	 * 
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
	 * Generate HTML form
	 * 
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

		// Prepare all needed information
		$entities   = array();
		$method     = array('method' => 'POST');
		$attributes = empty($attributes) ? $method : array_merge($attributes, $method);
		$open_tag   = ($multipart) ? form_open_multipart($url, $attributes) : form_open($url, $attributes);

		// Build the form entities
		foreach ($this->form_definitions as $label => $entity)
		{
			if ( ! in_array($label, $this->hidden_keys))
			{
				if (strpos($entity, ':hidden:') === 0)
				{
					$entity     = str_replace(':hidden:', '', $entity);
					$entities[] = $this->form_entity_prefix.$entity.$this->form_entity_suffix;
				}
				else
				{
					// Do we need to hide some label?
					if (strpos($label, ':hidden:') === 0)
					{
						$entities[] = $this->form_entity_prefix.$entity.$this->form_entity_suffix;
					}
					else
					{
						$formatted_label = form_label(ucfirst(str_replace(array('-', '_'), ' ', $label)));
						$entities[]      = $this->form_entity_prefix.$formatted_label.$entity.$this->form_entity_suffix;
					}
				}
			}
		}

		if (empty($this->form_submit)) $this->submit();

		$close_tag = form_close();

		// Finalize the form
		if ($as_array)
		{
			$form = array(
				'open_tag'  => $open_tag,
				'entities'  => $entities,
				'submit'    => $this->form_submit,
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
	 * Check form helper state
	 * 
	 * @return void
	 */
	protected function validate_form()
	{
		// Load the form helper
		if ( ! function_exists('form_open'))
		{
			$CI =& get_instance();
			$CI->load->helper('form');
		}

		return;
	}

	/**
	 * Return table collumns name
	 * 
	 * @return array
	 */
	protected function all_collumns()
	{
		// Determine whether gas property was a single instance or
		// a collection of instance(s)
		if (is_object($this->gas))
		{
			$collumns = $this->gas->meta->get('collumns');
		}
		elseif (is_array($this->gas))
		{
			$sample   = $this->gas;
			$gas      = array_shift($sample);
			$collumns = $gas->meta->get('collums');
		}
		else
		{
			// Null instance is not allowed
			throw new \BadMethodCallException('[html]Extension should receive a valid Gas Model instance');
		}

		return $collumns;
	}

	/**
	 * Generate label portion
	 * 
	 * @param  mixed
	 * @param  mixed
	 * @return void
	 */
	protected function human_name(&$v, $k)
	{
		$v = ucfirst(str_replace('_', '', $v));
	}

	/**
	 * Sorting keys for hidden keys
	 * 
	 * @param  mixed
	 * @param  mixed
	 * @return void
	 */
	protected function avoid_keys($v)
	{
		return( ! in_array($v, $this->hidden_keys));
	}
}