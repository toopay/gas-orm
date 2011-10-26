<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gasunittest extends CI_Controller {

	public $new_state;
	public $necesarry_item = array();
	public $title;

	function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->load->library('table');
		$this->load->helper('html');
		$this->load->helper('url');

		define('GAS_UNIT_TEST_VIEW', APPPATH.'views/gasunittest.php');
		define('GAS_NAME', 'gasunittest');

		$this->new_state = FALSE;
		$this->necessary_item = array('user', 'wife', 'kid', 'job');
		$this->title = 'Gas ORM Unit Testing Package';
	}

	public function index()
	{
		$this->_start();
		if($this->new_state) redirect(GAS_NAME.'/index');

		$all_tables_div = $this->_generate_all_tables();
		
		$attributes = array('class' => 'horizontal');

		$content = array(
				'Welcome, all necessary model were already created, which is : '.implode(', ', $this->necessary_item).'.',
				'Also all tables needed for this unit testing were created too, which is '.implode(', ', $this->necessary_item).' and job_user, here they structure looks like :',
				ul($all_tables_div, $attributes).'<br class="clear" />',
		);

		$this->load->view(GAS_NAME, array(
				'title' => $this->title,
				'content' => $this->_create_header()."\n".implode("\n",$content),
			)
		);
	}

	public function convention()
	{
		$gas = new Gas;

		$config = $gas->_config;

		$content = array(
				heading('Convention', 3),
				'<p>Gas makes some assumptions about your database structure. Each table should have primary key, default to <b>id</b>. You can set this dynamically in your Gas model by setting <b>$primary_key</b> property in your model class. Each table should have same name with its corresponding Gas model\'s name, but you still can set this dynamically in your Gas model by setting <b>$table</b> property in your model class.</p>',
				'<p>Gas model should be lower case, and it is came with <b>model_gas.php</b> convention for file-naming. You can change those suffix in <b>gas.php</b> under your config file. So you can still distict Gas models from your native CI models. Gas not enforce you to remove all your native CI model, since it still may usefull in some case for you.</p>',
				'<p>A typical model, for example user model, in <b>'.APPPATH.$config['models_path'].'/user'.$config['models_suffix'].'.php</b> is something like this :</p>',
				'<pre><code>'.implode('<br />', $this->_create_model('user', $config, FALSE)).'</code></pre>',
				'<p>Lets dig into it. First we have <b>$table</b> and <b>$primary_key</b> properties. This is just an example, which demonstrate that your table can have different name from your model\'s name, and your primary key can be different than <b>id</b>.',
				'<p>Second, <b>$this->_fields</b> properties filled with an array. It define, user\'s fields which you want to set validation rules in it. As you may notice, commonly we have used to have <b>char</b>, <b>int</b> and <b>auto</b> as our datat-types, so this should be easy with Gas field shorthand. You may notice <b>email</b> shorthand too. Now, since Gas utilize CI validation packages, you still can add other rules (required, matches, etc) by assign an array of your rules into second parameter. You even can add your custom callback function, with slightly different convention : add two parameter at your callback function, and if you want to do <b>set_message</b> method, add <b>$field</b> variable as third parameter, just like example above.</p>',
				'<p>Above model, have three relations properties : has one <b>wife</b>, has many <b>kid</b> and has many also belongs to (many to many relationship, joining by a pivot table) <b>job</b>.</p>',
				'<p>If you have those relations properties, corresponding model should reflects that relations too. In this packages, wife model have these structure : <p>',
				'<pre><code>'.implode('<br />', $this->_create_model('wife', $config, FALSE)).'</code></pre>',
				'<p>While kid model have these structure : <p>',
				'<pre><code>'.implode('<br />', $this->_create_model('kid', $config, FALSE)).'</code></pre>',
				'<p>And job model have these structure : <p>',
				'<pre><code>'.implode('<br />', $this->_create_model('job', $config, FALSE)).'</code></pre>',
				'<p>Notice that you doesn\'t need to have pivot table\'s defined, for many-to-many relationship, Gas automatically fix that, as long you have <b>modelA_modelB</b> convention name in your pivot table.</p>',
		);

		$this->load->view(GAS_NAME, array(
				'title' => $this->title,
				'content' => $this->_create_header()."\n".implode("\n",$content),
			)
		);
	}

	public function test_all()
	{
		$this->_start();
		if($this->new_state) redirect(GAS_NAME.'/test_all');

		$template = '<table border="0" cellpadding="10" cellspacing="3">
						<caption>&nbsp;</caption>
						<tbody style="display: table-row-group; vertical-align: middle; border-color: inherit;">
				    	{rows}
				    	<tr style="display: table-row; vertical-align: inherit; border-color: inherit;">
				        <td style="color:#000;background:#eaeaea;width:200px;">{item}</td>
				        <td style="color:#fff;background:#000;width:500px;">{result}</td>
				        </tr>
				    	{/rows}
				     	</tbody>
				</table>';

		$this->load->library('unit_test');
		$this->unit->set_template($template);
		$this->unit->use_strict(TRUE);

		$user = new User;
		$user->truncate();

		$someuser = $user->find(1000); 
		// Should return FALSE
		$this->unit->run($user->has_result(), FALSE, '[find_hasnt_result]', '-');
		// Should be an array
		$this->unit->run($someuser, 'is_bool', '[find_one_id]', 'FALSE is returned when we got no results at all');

		// Save several datas
		$input_datas = array(
			'valid' => array(
				array('id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@yahoo.com', 'username' => 'johndoe'),
				array('id' => 2, 'name' => 'Derek Jones', 'email' => 'derekjones@gmail.com','username' => 'derek'),
				array('id' => 3, 'name' => 'Frank Sinatra', 'email' => 'franks@whatever.com', 'username' => 'fsinatra'),
				array('id' => 4, 'name' => 'Chris Martin', 'email' => 'chris@yahoo.com', 'username' => 'cmartin'),
			),
			'invalid' => array(
				array('id' => 'not number', 'name' => 'more than max length which this field can hold, this is more than 40. Lets make it longggeeeeerrrrrrr. MOOOOOREEEEEE LOOONGGGER', 'email' => 'not[an]email','username' => 'me'),
			)
		);

		foreach($input_datas as $type => $input_data)
		{
			if($type == 'valid')
			{
				foreach($input_data as $post_data)
				{
					if(isset($_POST)) unset($_POST);
					
					$_POST = $post_data;

					foreach($post_data as $key => $data) $user->$key = $data;

					$affected_rows = $user->save(TRUE);

					// Should affect 1 row
					$this->unit->run($affected_rows, 1, '[save_valid_data]', '$_POST contain : '.implode(', ', $post_data).'.');
				}
			}
			elseif($type == 'invalid')
			{
				foreach($input_data as $post_data)
				{
					if(isset($_POST)) unset($_POST);

					$_POST = $post_data;

					foreach($post_data as $key => $data) $user->$key = $data;

					$affected_rows = $user->save(TRUE);

					// Should result FALSE
					$this->unit->run($affected_rows, FALSE, '[save_invalid_data]', 'Error message was : '.$user->errors('<b>','</b>'));
				}
			}
		}

		$users = $user->all(); 
		
		// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[all_has_result]', '-');
		// Should be an array
		$this->unit->run($users, 'is_array', '[all_returns_array_of_object]', '-');
		// Should contain 4, because we just adding 4 valid entries.
		$this->unit->run(count($users), 4, '[all_has_counts]', '-');

		foreach($users as $single_user)
	    {
	    	// Should be an object
	    	$this->unit->run($single_user, 'is_object', '[all_returns_array_of_object]', '-');

	    	// Should be an array
	    	$this->unit->run($single_user->to_array(), 'is_array', '[all_results_item_to_array]', '-');
	    }
	   
	   	$firstuser = $user->first(); 
	   	// Should be an object
	    $this->unit->run($firstuser, 'is_object', '[first_is_object]', '-');
	    // Should be user with id 1
	    $this->unit->run($firstuser->id, '1', '[first_have_id_1]', '-');
	   
	   	$lastuser = $user->last();
	   	// Should be an object
	    $this->unit->run($lastuser, 'is_object', '[last_is_object]', '-');
	    // Should be user with id 1
	    $this->unit->run($lastuser->id, '4', '[last_have_id_4]', '-');
	   	
		$max = $user->max();
		// Should be 4
	    $this->unit->run((int)$max->id, 4, '[max_of_id]', '-');

		$min = $user->min(); 
		// Should be 1
	    $this->unit->run((int)$min->id, 1, '[min_of_id]', '-');

		$avg = $user->avg('id', 'average_id');
		// Should be 2.5
	    $this->unit->run((float)$avg->average_id, 2.5000, '[avg_of_id]', '(1 + 2 + 3 + 4) / 4 = 2.5');
		
		$sum = $user->sum('id', 'sum_of_id');
		// Should be 10 
	    $this->unit->run((int)$sum->sum_of_id, 10, '[sum_of_id]', '(1 + 2 + 3 + 4) / 4 = 2.5');
	   	
	   	$someuser = $user->find(1); 
	   	// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[find_has_result]', '-');
		// Should be an object
		$this->unit->run($someuser, 'is_object', '[find_one_id]', 'If we assign only one id in find, than it will return an object');
		// Should be 'John Doe'
		$this->unit->run($someuser->name, 'John Doe', '[find_found_john]', 'Because user with id 1 is John Doe');

		$someusers = $user->find(1, 2, 3); 
	   	// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[find_has_result]', '-');
		// Should be an array
		$this->unit->run($someusers, 'is_array', '[find_several_id]', '-');
		// Should be 3, because we search for 3 valid entries.
		$this->unit->run(count($someusers), 3, '[find_totals]', '-');
		
		$someusers = $user->find(1, 100, 1000, 10000); 
		// Should be an array
		$this->unit->run($someusers, 'is_array', '[find_several_id]', 'Even if the result is one, but it will result an array, because we assign more than one id in find parameter');
		// Should be 1, because we search for 1 valid entries and 3 invalid id.
		$this->unit->run(count($someusers), 1, '[find_several_id]', '-');
	   	
		$someusers = $user->find_by_email('johndoe@yahoo.com'); 
		// Should be an array, because we didnt specify the limit
		$this->unit->run($someusers, 'is_array', '[find_by_something]', 'Without passing limit as second params, Gas will always return an array');
		// Should be 1, because we search for 1 valid entries.
		$this->unit->run(count($someusers), 1, '[find_by_something]', '-');

		$someuser = $user->find_by_email('derekjones@gmail.com', 1); 
		// Should be an object, because we specify the limit to 1
		$this->unit->run($someuser, 'is_object', '[find_by_something]', 'By passing limit = 1, object will returned instead an array');
		// Should be Derek Jones, because we search for user with id 2.
		$this->unit->run($someuser->name, 'Derek Jones', '[find_by_something]', '-');
		
		$someusers = $user->group_by('email')->all();
		// Should be an array, because we use 'all'
		$this->unit->run($someusers, 'is_array', '[ci_ar_group_by]', 'Grouped By email');
		// Should be 4, because we use 'all', we just grouped/sorted it by email.
		$this->unit->run(count($someusers), 4, '[ci_ar_group_by]', '-');

		$someusers = $user->like('email', 'yahoo.com')->all();
		// Should be an array, because we use 'all'
		$this->unit->run($someusers, 'is_array', '[ci_ar_like]', 'Where email like "yahoo.com"');
		// Should be 2, because there are two user created with yahoo email
		$this->unit->run(count($someusers), 2, '[ci_ar_like]', '-');

		// Save several datas, for other table as well.
		$secondary_datas = array(
			'wife' => array(
				array('id' => 1, 'user_id' => 1, 'name' => 'Pat Doe', 'hair_color' => 'black'),
				array('id' => 2, 'user_id' => 2, 'name' => 'Lourie Jones', 'hair_color' => 'black'),
				array('id' => 3, 'user_id' => 3, 'name' => 'Lily Sinatra', 'hair_color' => 'blonde'),
			),
			'kid' => array(
				array('id' => 1, 'user_id' => 1, 'name' => 'Daria Doe', 'age' => 1),
				array('id' => 2, 'user_id' => 1, 'name' => 'John Doe Jr', 'age' => 2),
				array('id' => 3, 'user_id' => 2, 'name' => 'Abraham Jones', 'age' => 3),
				array('id' => 4, 'user_id' => 2, 'name' => 'Chyntia Jones', 'age' => 4),
				array('id' => 5, 'user_id' => 2, 'name' => 'Laura Jones', 'age' => 5),
				array('id' => 6, 'user_id' => 3, 'name' => 'Dolly Sinatra', 'age' => 1),
			),
			'job' => array(
				array('id' => 1, 'name' => 'Developer', 'description' => 'Awesome job, but sometimes makes you bored.'),
				array('id' => 2, 'name' => 'Politician', 'description' => 'This is not really a job.'),
				array('id' => 3, 'name' => 'Accountant', 'description' => 'Boring job, but you will get free snack at lunch.'),
				array('id' => 4, 'name' => 'Musician', 'description' => 'Make sure your voice is good.'),
			),
			'job_user' => array(
				array('user_id' => 1, 'job_id' => 1),
				array('user_id' => 1, 'job_id' => 2),
				array('user_id' => 2, 'job_id' => 1),
				array('user_id' => 2, 'job_id' => 3),
				array('user_id' => 4, 'job_id' => 4),
			),

		);

		$wife = new Wife;
		$wife->truncate();

		$kid = new Kid;
		$kid->truncate();

		$job = new Job;
		$job->truncate();

		$this->db->truncate('job_user');

		foreach($secondary_datas as $type => $input_data)
		{
			if($type == 'job_user')
			{
				foreach($input_data as $post_data) $this->db->insert('job_user', $post_data); 
			}
			else
			{
				foreach($input_data as $post_data)
				{
					foreach($post_data as $key => $data) $$type->$key = $data;

					$affected_rows = $$type->save();

					// Should result TRUE
					$this->unit->run($affected_rows, 1, '[save_valid_data]', '-');
				}
			}
		}

		$someuser = $user->find(1); 
	   	// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[find_has_result]', '-');
		// Should be 'John Doe'
		$this->unit->run($someuser->name, 'John Doe', '[find_found_john]', 'Because user with id 1 is John Doe');

		// Should be an object, because this is one-to-one relationship
		$this->unit->run($someuser->wife, 'is_object', '[one_to_one]', '-');
		// Should be Pat Doe
		$this->unit->run($someuser->wife->name, 'Pat Doe', '[one_to_one]', '-');

		$somewife = $someuser->wife;
		$somewife->name = 'Patricia Doe';
		// Should be 1 row affected
		$this->unit->run($somewife->save(), 1, '[one_to_one]', 'Update wife tables from user model relations');
		
		// Should be an array, because this is one-to-many relationship
		$this->unit->run($someuser->kid, 'is_array', '[one_to_many]', '-');

		foreach($someuser->kid as $kid)
		{
			$contain_family_name = (bool) (strpos($kid->name, 'Doe') !== FALSE);
			// Should be TRUE, because John Doe kid were Daria Doe and John Doe Jr, which contain Doe in their name
			$this->unit->run($contain_family_name, TRUE, '[one_to_many]', '-');
		}

		$someuser = $user->find(4); 
	   	// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[find_has_result]', '-');
		
		// Should be FALSE, because user 4, didnt have wife
		$this->unit->run($someuser->wife, FALSE, '[one_to_one]', 'When there is no result, then FALSE will returned');
		// Should be TRUE, because user 4, didnt have kids
		$this->unit->run(empty($someuser->kid), TRUE, '[one_to_many]', 'When there are no result, then empty array will returned');

		// Should be an array, because this is many-to-many relationship
		$this->unit->run($someuser->job, 'is_array', '[many_to_many]', '-');
		// Should be one, because this user only have one job
		$this->unit->run(count($someuser->job), 1, '[many_to_many]', '-');

		foreach($someuser->job as $job)
		{
			$is_musician = (bool) (strpos($job->description, 'voice') !== FALSE);
			// Should be TRUE, because Chris martin is Coldplay vocalis
			$this->unit->run($is_musician, TRUE, '[many_to_many]', '-');

			$job->description = 'Only Coldplay can actually called Musician.';
			// Should be 1 row affected, Coldplay is my favourite band
			$this->unit->run($job->save(), 1, '[many_to_many]', 'Update a many-to-many table');
		}

		$allinone = $user->with('wife', 'kid', 'job')->all();
		// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[eager_loading]', '-');
		// Should be an array
		$this->unit->run($allinone, 'is_array', '[eager_loading]', '-');
		// Should contain 4, because we just adding 4 valid entries.
		$this->unit->run(count($allinone), 4, '[eager_loading]', '-');

		foreach($allinone as $one)
		{
			// Should be a string, contain user\'s name
			$this->unit->run($one->name, 'is_string', '[eager_loading_result]', '-');
			// Should be an object of wife, one-to-one relationship
			$this->unit->run($one->wife, 'is_object', '[eager_loading_result]', '-');
			// Should be an array of kid\'s object, one-to-many relationship
			$this->unit->run($one->kid, 'is_array', '[eager_loading_result]', '-');
			// Should be an array of job\'s object, many-to-many relationship
			$this->unit->run($one->job, 'is_array', '[eager_loading_result]', '-');

			if($one->id == 3)
			{
				$some_wife = $one->wife;
				$some_wife->hair_color = 'white';
				// Should be an 1 row affected, because user 3 really have 1 wife
				$this->unit->run($some_wife->save(), 1, '[eager_loading_update]', '-');
			}
		}

		$someuser = $user->find(3); 
	   	// Should return TRUE
		$this->unit->run($user->has_result(), TRUE, '[eager_loading]', '-');
		// Should return white, because at loop above, we change it :P
		$this->unit->run($someuser->wife->hair_color, 'white', '[eager_loading]', '-');
		$someuser->wife->hair_color = 'brunette';
		// Should return 1 row affected, because woman with white hair is wick
		$this->unit->run($someuser->wife->save(), 1, '[eager_loading]', '-');
		
		unset($_POST);

		$content = array(
				heading('Unit Testing', 3),
				$this->unit->report(),
		);

		$this->load->view(GAS_NAME, array(
				'title' => $this->title,
				'content' => $this->_create_header()."\n".implode("\n",$content),
			)
		);
	}

	public function end()
	{
		$info = array();

		$info[] = '<h3>Deleting all auto-created files and tables created by <i>Gas ORM Unit Testing Package</i></h3>';

		$gas = new Gas;

		$config = $gas->_config;

		$info[] = '<b>Scanning models directories...</b>';

		foreach($this->necessary_item as $item)
		{
			$model = APPPATH.$config['models_path'].'/'.$item.$config['models_suffix'].'.php';
			
			if(file_exists($model ))
			{
				$info[] = 'Deleting '.$item.'\'s model : '.$model;
				unlink($model);
			}
		}

		$info[] = "\n".'<b>Scanning views directories...</b>';
		if(file_exists(GAS_UNIT_TEST_VIEW))
		{
			unlink(GAS_UNIT_TEST_VIEW);
			$info[] = 'Deleting : '.GAS_UNIT_TEST_VIEW;
		}

		$all_items = $this->necessary_item; 
		$all_items[] = 'job_user';

		$info[] = "\n".'<b>Scanning database tables...</b>';

		foreach($all_items as $item) 
		{
			$this->dbforge->drop_table($item);
			$info[] = 'Droping '.$item.'\'s table if exists';
		}

		echo '<pre>'.implode("\n", $info)."\n\n".'Done, '
			.'all Gas ORM unit testing files and tables was sucessfully removed. To start all over again, go to : <a href="'.site_url(GAS_NAME).'">'.GAS_NAME.'</a></pre>';
	}

	private function _start()
	{
		$gas = new Gas;

		foreach($this->necessary_item as $item)
		{
			$gas->table_exists($item) or $this->_create_table($item);

			if( ! in_array($item, array_keys($gas->list_models())))
			{
				$this->_create_model($item, $gas->_config);
				$gas->scan();
				$gas->load($item);
			}
		}
		
		$gas->table_exists('job_user') or $this->_create_table('job_user');
		if( ! file_exists(GAS_UNIT_TEST_VIEW)) $this->_create_view();

		return $this->new_state;
	}

	private function _generate_all_tables()
	{
		$tables = array();

		foreach($this->necessary_item as $item)
		{
			$results = array();

			$instance = new $item;
			$instances = $instance->all();

			$data = array($instance->list_fields($instance->table));

			if($instance->count() > 0)
			{
				foreach($instances as $result) $data = array_merge($data, array($result->to_array()));
			}
			else
			{
				$empty = array();

				foreach($instance->list_fields($instance->table) as $column) $empty[] = '-';

				$data = array_merge($data, array($empty));
			}
			
			$tables[] = heading($item.'\'s table', 4)."\n".$this->table->generate($data);
		}

		return $tables;
	}

	private function _create_model($model, $config, $write = TRUE)
	{
		$this->new_state = TRUE;

		$file = APPPATH.$config['models_path'].'/'.$model.$config['models_suffix'].'.php';

		$user = 'array('."\n"
				."\t\t\t\t".'\'has_one\' => array(\'wife\' => array()),'."\n"
				."\t\t\t\t".'\'has_many\' => array(\'kid\' => array()),'."\n"
				."\t\t\t\t".'\'has_and_belongs_to\' => array(\'job\' => array()),'."\n"
				."\t".');';

		$wife = 'array('."\n"
				."\t\t\t\t".'\'belongs_to\' => array(\'user\' => array()),'."\n"
				."\t".');';

		$kid = 'array('."\n"
				."\t\t\t\t".'\'belongs_to\' => array(\'user\' => array()),'."\n"
				."\t".');';

		$job = 'array('."\n"
				."\t\t\t\t".'\'has_and_belongs_to\' => array(\'user\' => array()),'."\n"
				."\t".');';

		$user_table = "\n"
				."\t".'public $table = \'user\';'."\n";

		$user_primary_key = "\n"
				."\t".'public $primary_key = \'id\';'."\n";

		$user_validation = "\t\t".'$this->_fields = array('."\n"
				."\t\t\t".'\'id\' => Gas::field(\'auto[3]\'),'."\n"
				."\t\t\t".'\'name\' => Gas::field(\'char[40]\'),'."\n"
				."\t\t\t".'\'email\' => Gas::field(\'email\'),'."\n"
				."\t\t\t".'\'username\' => Gas::field(\'char[10]\', array(\'callback_username_check\')),'."\n"
				."\t\t".');';

		$user_callback = "\t".'public function username_check($field, $val)'."\n"
				."\t".'{'."\n"
				."\t\t".'if($val == \'me\')'."\n"
				."\t\t".'{'."\n"
				."\t\t\t".'$this->set_message(\'username_check\', \'The %s field cannot fill by "me"\', $field);'."\n"."\n"
				."\t\t\t".'return FALSE;'."\n"
				."\t\t".'}'."\n"
				."\t\t".'return TRUE;'."\n"
				."\t".'}';

		$relations = array(
			$model => "\t".'public $relations = '.$$model,
		);

		$table = ($model == 'user') ? $user_table : '';
		$primary_key = ($model == 'user') ? $user_primary_key : '';
		$validation = ($model == 'user') ? $user_validation : '';
		$callback = ($model == 'user') ? $user_callback : '';

		$model_convention = array(
				'<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');',
				'',
				'class '.ucfirst($model).' extends Gas {',
				''.
				$table.$primary_key,
				$relations[$model],
				'',
				"\t".'function _init()',
				"\t".'{',
				$validation,
				"\t".'}',
				''.$callback,
				'}',
		);
		
		if( ! $write)
		{
			array_walk_recursive($model_convention, GAS_NAME.'::_code_to_string');
			return $model_convention;
		}

		$this->_create_file($file, $model_convention);
	}

	private function _create_table($table)
	{
		$this->new_state = TRUE;

		$user = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 3,
						'unsigned' => TRUE,
						'auto_increment' => TRUE,
					),
                    'name' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => '40',
                    	'default' => '',
                    ),
                    'email' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => '40',
                    	'default' => '',
                    ),
                    'username' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => '10',
                    	'default' => '',
					),
        );

        $wife = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 3,
						'unsigned' => TRUE,
						'auto_increment' => TRUE,
					),
                    'user_id' => array(
                    	'type' => 'INT',
						'constraint' => 3,
					),
                    'name' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => '40',
                    	'default' => '',
                    ),
                    'hair_color' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => '20',
                    	'default' => '',
					),
        );

        $kid = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 3,
						'unsigned' => TRUE,
						'auto_increment' => TRUE,
					),
                    'user_id' => array(
                    	'type' => 'INT',
						'constraint' => 3,
					),
                    'name' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => 40,
                    	'default' => '',
                    ),
                    'age' => array(
                    	'type' => 'INT',
                    	'constraint' => 1,
					),
        );

        $job = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 3,
						'unsigned' => TRUE,
						'auto_increment' => TRUE,
					),
                   	'name' => array(
                    	'type' => 'VARCHAR',
                    	'constraint' => 40,
                    	'default' => '',
                    ),
                    'description' => array(
                    	'type' => 'TEXT',
                    	'constraint' => 40,
                    	'default' => '',
                    ),
        );

        $job_user = array(
					'user_id' => array(
						'type' => 'INT',
						'constraint' => 3,
					),
                   	'job_id' => array(
						'type' => 'INT',
						'constraint' => 3,
					),
        );

		$this->dbforge->add_field($$table);
        if (key($$table) == 'id') $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($table) or show_error('Something wrong! Forge fails when try create '.$table);
	}

	private function _create_view()
	{
		$this->new_state = TRUE;

		$path = GAS_UNIT_TEST_VIEW;
		$view_html = array(
				'<!DOCTYPE HTML>',
				'<html>',
				'<head>',
				'<title><?php echo $title ?></title>',
				'<style type="text/css">',
   				'body {margin:0 auto;width:960px;background:#000;color:#484848;font-family:Tahoma, Arial;text-shadow: -0.1px -0.1px 1px #eaeaea;}',
   				'.content {background:#fff;margin-top:20px;padding:30px;-moz-border-radius: 25px;border-radius: 25px;}',
   				'caption {display: table-caption; text-align: -webkit-center; }',
   				'table {text-align: left; font-size: 13px; }',
   				'table {border-collapse: collapse; border-spacing: 0; } user agent stylesheet table {border-collapse: separate; border-spacing: 2px; }',
   				'table thead{background-color:#000;color:#fff;}',
   				'table tbody{background-color:#787878;color:#eaeaea;}',
   				'ul.horizontal{margin:0;list-style:none;margin-left:-35px !important;}',
   				'ul.horizontal li{float:left;padding-right:10px;}',
   				'a.menu{padding: 10px; background: black; font-size: 13px; font-weight: bold; color: white; text-shadow: -1px -1px 1px #515151 !important; text-decoration: none; -moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px;}',
   				'a.menu:hover{background: #787878;}',
   				'a.unittest{background: #a0df13;}',
   				'a.exit{background: #dd0000;}',
   				'code {display: block; background: black; padding: 10px; color: white; font-family: monospace; font-size: 1em; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; margin-bottom: 15px; text-shadow: 0 0 1px #000;}',
   				'br.clear {clear:both;}',
 				'</style>',
				'</head>',
				'<body>',
				'<div class="content">',
				'<?php echo $content ?>',
				'</div>',
				'</body>',
				'</html>',
			);

		$this->_create_file($path, $view_html);
	}

	private function _create_header()
	{
		$css_menu = array('class' => 'horizontal');
		$css_anchor = array('class' => 'menu');
		$css_unittest = array('class' => 'menu unittest');
		$css_exit = array('class' => 'menu exit');

		$menu = array(
			anchor(GAS_NAME.'/index', 'Gas ORM Start Page', $css_anchor),
			anchor(GAS_NAME.'/convention', 'Gas ORM Convention', $css_anchor),
			anchor(GAS_NAME.'/test_all', 'Run Unit Test', $css_unittest),
			anchor(GAS_NAME.'/end', 'Delete All Auto-created Files And Tables', $css_exit),
		);

		$header = array(
			'<h1>'.$this->title.'</h1>',
			ul($menu, $css_menu).'<br class="clear" /><br /><br />'."\n",
		);
		
		return implode("\n",$header);
	}

	private function _create_file($path, $content)
	{
		$fh = fopen($path, 'w') or show_error('Something wrong! Cannot open '.$path);

		$data = '';
		
		foreach($content as $line) $data .= $line."\n";
		
		fwrite($fh, $data);

		fclose($fh);
	}

	private function _code_to_string(&$v, $k)
	{
		$s = '<span style="color:#dd0000">';
		$a = '<span style="color:#9999CC">';
		$e = '</span>';
		$v = str_replace(array('<', '=>', 'extends', 'public', 'array', 'function', 'class'), 
						array('&lt;', $s.'=>'.$e, $s.'extends'.$e, $a.'public'.$e, $a.'array'.$e, $a.'function'.$e, $a.'class'.$e), 
						$v);
	}
}