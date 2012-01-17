<?php namespace Model\Job;

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
 * @subpackage  Gas ORM Model
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
 * Model\Job\User Class.
 *
 * This dummy job\user model, serve all test corresponding with Job_user table and its relation
 *
 * @package     Gas ORM
 * @version     2.0.0
 */

use \Gas\Core;
use \Gas\ORM;

class User extends ORM {

	/**
	 * Set up method for unit testing
	 */
	public static function setUp()
	{
		// Generate a reflection
		$reflection  = self::make();
		$table       = $reflection->validate_table()->table;
		$primary_key = $reflection->primary_key;

		// Drop if table exists
		self::forge()->drop_table($table);

		//Build the new one now
		foreach ($reflection->meta->get('fields') as $field => $rule) 
		{
			$annotation     = $rule['annotations'];
			$fields[$field] = Core::identify_annotation($annotation);
		}

		self::forge()->add_field($fields);
		self::forge()->add_key($primary_key, TRUE);
		self::forge()->create_table($table);

		// Then add some dummy data
		$data = array(
		    array('user_id' => 1, 'job_id' => 3),
		    array('user_id' => 1, 'job_id' => 2),
		    array('user_id' => 2, 'job_id' => 1),
		    array('user_id' => 3, 'job_id' => 4),
		    array('user_id' => 4, 'job_id' => 4),
		);

		self::insert_batch($data); 
	}

	function _init()
	{
		// Define relationships
		self::$relationships = array(
			'user' => ORM::belongs_to('\\Model\\User'),
			'job'  => ORM::belongs_to('\\Model\\Job'),
		);

		// Define fields definition
		self::$fields = array(
			'id'         => ORM::field('auto[3]'),
			'user_id'    => ORM::field('int[3]'),
			'job_id'     => ORM::field('int[3]'),
		);
	}
}