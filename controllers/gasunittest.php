<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Gas Unit Testing Package.
 * 
 * Contain Convention, Documentation and unit testing tasks.
 *
 * @package     Gas Library
 */

class Gasunittest extends CI_Controller {

	public $new_state;

	public $necesarry_item = array();

	public $title;

	public $dummy_users = array();

	function __construct()
	{
		parent::__construct();

		if (defined('FCPATH') and is_dir(FCPATH.'sparks'.DIRECTORY_SEPARATOR.'Gas-ORM'))
		{
			$this->load->spark('Gas-ORM/1.4.0');
		}

		$gas = new Gas;

		$this->db = $gas->db();

		$this->load->dbforge();

		$this->load->library('table');

		$this->load->helper('html');

		$this->load->helper('url');

		define('GAS_UNIT_TEST_VIEW', APPPATH.'views/gasunittest.php');

		define('GAS_NAME', 'gasunittest');

		$this->new_state = FALSE;

		$this->necessary_item = array('user', 'wife', 'kid', 'job', 'role', 'user_role', 'comment');

		$this->title = 'Gas ORM Version '.Gas::version().' Unit Testing Package';

		$this->dummy_users = array(

				array('id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@john.com', 'username' => 'johndoe'),

				array('id' => 2, 'name' => 'Derek Jones', 'email' => 'derekjones@world.com','username' => 'derek'),

				array('id' => 3, 'name' => 'Frank Sinatra', 'email' => 'franks@world.com', 'username' => 'fsinatra'),

				array('id' => 4, 'name' => 'Chris Martin', 'email' => 'chris@coldplay.com', 'username' => 'cmartin'),

			);

	}

	public function index()
	{
		$this->_start();

		if ($this->new_state) redirect(GAS_NAME.'/index');
		
		$all_tables_div = $this->_generate_all_tables();
		
		$attributes = array('class' => 'horizontal');

		$content = array(

				'Welcome, all necessary model were already created, which is : '.implode(', ', $this->necessary_item).'.',

				'Also all tables needed for this unit testing were created too, which is '.implode(', ', $this->necessary_item).' and job_user, here they structure looks like :',

				ul($all_tables_div, $attributes).'<br class="clear" /><br />',

				'Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages of its validator class. Gas provide methods that will map your database table and its relation, into accesible object.<br /><br />',

				heading('Documentation', 3),

				'For full documenation, convention and example go to : <a href="http://gasorm-doc.taufanaditya.com" target="_blank" class="link">Home of Gas ORM</a>.',

				heading('Repositories', 3),

				'If you have a request or found any issues, you can submit it : <a href="https://github.com/toopay/CI-GasORM-Library" target="_blank" class="link">Gas on GitHub</a>.',

				heading('Discussion', 3),

				'Join on-going discussion (and release history) on : <a href="http://codeigniter.com/forums/viewthread/202669/" target="_blank" class="link">CodeIgniter forum</a>.',

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

		$config = $gas->get_config();

		$content = array(

				heading('Convention Documentation', 3),

				'For complete convention and detailed explanation go to : <a href="http://gasorm-doc.taufanaditya.com/convention.html#convention" target="_blank" class="link">Convention Section</a>.',

				heading('Quick Brief', 3),

				'<p>Gas makes some assumptions about your database structure. Each table should have primary key, default to <b>id</b>. You can set this dynamically in your Gas model by setting <b>$primary_key</b> property in your model class. Each table should have same name with its corresponding Gas model\'s name, but you still can set this dynamically in your Gas model by setting <b>$table</b> property in your model class.</p>',

				'<p>Gas model should be lower case, and it is came with <b>model_gas.php</b> convention for file-naming. You can change those suffix in <b>gas.php</b> under your config file. So you can still distinct Gas models from your native CI models. Gas not enforce you to remove all your native CI model, since it still may usefull in some case for you.</p>',

				'<p>A typical model, for example user model, in <b>'.APPPATH.$config['models_path'].'/user'.$config['models_suffix'].'.php</b> is something like this :</p>',

				'<pre><code>'.implode('<br />', $this->_create_model('user', $config, FALSE)).'</code></pre>',
				'<p>Lets dig into it. First we have <b>$table</b> and <b>$primary_key</b> properties. This is just an example, which demonstrate that your table can have different name from your model\'s name, and your primary key can be different than <b>id</b>.',

				'<p>Second, <b>$this->_fields</b> properties filled with an array. It define, user\'s fields which you want to set validation rules in it. As you may notice, commonly we have used to have <b>char</b>, <b>int</b> and <b>auto</b> as our datat-types, so this should be easy with Gas field shorthand. You may notice <b>email</b> shorthand too. Now, since Gas utilize CI validation packages, you still can add other rules (required, matches, etc) by assign an array of your rules into second parameter. You even can add your custom callback function, with slightly different convention : add two parameter at your callback function, and if you want to do <b>set_message</b> method, add <b>$field</b> variable as third parameter, just like example above.</p>',

				'<p>To provide a full controll to your models, Gas provide several callbacks, so you can hook into Gas lifecycle. Available callbacks were : <b>_before_check</b>, <b>_after_check</b>, <b>_before_save</b>, <b>_after_save</b>, <b>_before_delete</b>, <b>_after_delete</b>. If you need to run something only once, before anything else happen, use _init instead (_init works in similar way with class constructor)</p>',

				'<p>Above model, have several relations properties : has one <b>wife</b>, has many <b>kid</b>, has many <b>comment</b>, has many (many to many relationship, joined by an intermediate table) <b>role</b> and has many also belongs to (many to many relationship, joined by a pivot table) <b>job</b>.</p>',
				'<p>If you have those relations properties, corresponding model should reflects that relations too. In this packages, wife model have these structure : <p>',

				'<pre><code>'.implode('<br />', $this->_create_model('wife', $config, FALSE)).'</code></pre>',

				'<p>While kid model have these structure : <p>',

				'<pre><code>'.implode('<br />', $this->_create_model('kid', $config, FALSE)).'</code></pre>',

				'<p>While job model have these structure : <p>',

				'<pre><code>'.implode('<br />', $this->_create_model('job', $config, FALSE)).'</code></pre>',

				'<p>Notice that you doesn\'t need to have pivot table\'s defined, for many-to-many relationship (has_and_belongs_to), Gas automatically fix that, as long you have <b>modelA_modelB</b> convention name in your pivot table. You could also overide this behaviour to fit with your needs, by set <b>foreign_key</b> and/or <b>foreign_table</b>, just make sure your corresponding model represent those relationship too.</p>',

				'<p>While role model have these structure : <p>',

				'<pre><code>'.implode('<br />', $this->_create_model('role', $config, FALSE)).'</code></pre>',

				'<p>Notice that both <b>user</b> model and <b>role</b> model were linked via intermediate table, which is <b>user_role</b>, so if you build this relation, make sure you set <b>through</b> in each relations properties. This is a different case, compared to <b>has_and_belongs_to</b>, because the intermediate table also have its model and other fields, despite both were a many-to-many relationship. Last thing you should notice, because both tables (user and role) have unstandard convention of foreign key (u_id and r_id), both model specify each own <b>foreign_key</b> in their relationship settings. If you follow <b>model_pk</b> convention, you didnt need to set this.</p>',

				'<p>and comment model have these structure : <p>',

				'<pre><code>'.implode('<br />', $this->_create_model('comment', $config, FALSE)).'</code></pre>',

				'<p>Here also you found, that Gas ORM support self-referential association (which mean, you can also store adjacency column/data). In this case, each comment can be a reply to other comment, mean they reference themself within one table. If you have this kind of table, you can working on it by specify <b>self</b> option in your relations properties. Self-referential works as you need, means it support all of defined relation types. You may also use eager loading as well.</p>',
		);

		$this->load->view(GAS_NAME, array(

				'title' => $this->title,

				'content' => $this->_create_header()."\n".implode("\n",$content),

			)
		);
	}

	public function documentation()
	{
		$str_code = file_get_contents(__FILE__);

		$finder_example = '';

		$write_example = '';

		$trans_example = '';

		$relations_example = '';

		$eagerloading_example = '';

		$codes = array('finder', 'write', 'trans', 'relations', 'eagerloading');

		foreach ($codes as $code)
		{
			$code_name = $code.'_example';

			$code_pattern = '/code-'.$code.':([^\n]+)&gt;\*\/(.+)\/\*/';

			if (preg_match_all($code_pattern, htmlspecialchars($str_code), $m) and count($m) == 3)
			{
				$keys = $m[1];

				$vals = $m[2];

				array_walk_recursive($vals, GAS_NAME.'::_code_to_string');

				$raw_code = array_combine($keys, $vals);


				foreach ($raw_code as $comment => $line)
				{
					$$code_name .= '<span style="color: #484848; font-style: italic;">// '.$comment.'</span>'."\n"
										.$line."\n\n";
				}
			}
		}

		$overview = array(

				'$user = new User;'."\n",

				'<span:comment>// Now you can use any available Gas method, or your User models public method</span>',

				'$user1 = $user->find(1);'."\n",

				'<span:comment>// Below implementation is actually similar with above</span>',

				'$user1 = Gas::factory(\'user\')->find(1);'

				);
		
		array_walk_recursive($overview, GAS_NAME.'::_code_to_string');

		$content = array(

				heading('Usage Documentation', 3),

				'For complete API usage and detailed explanation go to : <a href="http://gasorm-doc.taufanaditya.com/quickstart.html#quick-start" target="_blank" class="link">Quickstart Section</a>.',

				heading('Quick Brief', 3),

				'<p>Before start using any of Gas available method, you should have a gas model, which follow <a href="/'.GAS_NAME.'/convention" class="link">Gas standard model convention</a>. Then, you can start using it either by instantiate new Gas object or by using factory interface, eg :</p>',

				'<pre><code>'.implode("\n",$overview).'</code></pre>',

				heading('Fetch records', 3),

				'<p>You can do almost anything you want : find by primary key, find where arguments, join, aggregates and so on. Heres some basic :</p>',

				'<pre><code>'.$finder_example.'</code></pre>',

				heading('Write Operations (Insert, Update, Delete)', 3),

				'<p>Since Gas utilize CI Form Validation, data validation process will not longer need draw a dragon in your code-blocks. Validation is an optional feature, soon you set up your _fields at _init method, your fields will be validated if you try to save a record(s) and passed TRUE parameter into save method. Update and delete process will be follow your recorded logic.</p>',

				'<pre><code>'.$write_example.'</code></pre>',

				heading('Transaction', 3),

				'<p>Like CI Active Record, Gas also inherit those SQL transaction as well, with extra intelegently support AR pattern in transaction blocks.</p>',

				'<pre><code>'.$trans_example.'</code></pre>',

				heading('Relationship (One-To-One, One-To-Many, Many-To-Many)', 3),

				'<p>Gas supported three type of table relationship, one-to-one relationship, one-to-many relationship and many-to-many relationship. All you have to do, is to define your table relations at <b>$relations</b> properties in your model.</p>',

				'<pre><code>'.$relations_example.'</code></pre>',

				heading('Eager Loading (N+1)', 3),

				'<p>Gas support eager loading, so you can improve your relationship queries. Eager loading works for all <b>$relations</b> properties that you defined. Based by examples above, you can eager loading any types of relationship tables, using <b>with()</b> method.</p>',

				'<pre><code>'.$eagerloading_example.'</code></pre>',
		);

		$this->load->view(GAS_NAME, array(

				'title' => $this->title,

				'content' => $this->_create_header()."\n".implode("\n",$content),

			)
		);
	}

	public function extension()
	{
		$user = new User;

		Gas::load_extension('dummy');

		$user->truncate();

		foreach ($this->dummy_users as $new_user) $user->fill($new_user)->save();

		// The convention, to call an extension was :
		// [instance]->[extension]->[instance_method]->[extension_method]
		//
		// So, using factory method, bellow syntax is equivalent with :
		// echo Gas::factory('user')->dummy->all()->explain('something');
		echo $user->dummy->all()->explain('something');

		echo '<pre>'.anchor(GAS_NAME.'/index', 'Back to start page').'</pre>';
	}

	public function test_all()
	{
		$this->_start();

		if ($this->new_state) redirect(GAS_NAME.'/test_all');

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

		Gas::factory('user')->truncate();

		/*<code-write:Suppose you have this $_POST value from some form'>*/$_POST = array('id' => null, 'name' => 'Mr. Foo', 'email' => 'foo@world.com', 'username' => 'foo');/*<endcode>*/

		/*<code-write:Instantiate User object>*/$new_user = new User;/*<endcode>*/

		/*<code-write:You can easily attach $_POST using 'fill' method, to set a datas for next 'save' method>*/$new_user->fill($_POST, TRUE);/*<endcode>*/

		

		/*<code-write:If something goes wrong in validation process, you can retrieve error via 'errors' method>*/if ( FALSE == ($affected_rows = $new_user->save(TRUE))) die($new_user->errors());/*<endcode>*/

		// Should affect 1 row
		$this->unit->run($affected_rows, 1, '[save]', 'Write operation always return affected rows.');

		/*<code-write:From last created record, using 'last_id' method, eg : will return '1', because above is first record>*/$new_id = $new_user->last_id();/*<endcode>*/

		// Should be 1, because this is the first record we created
		$this->unit->run($new_id, 1, '[last_id]', 'Retrieve new created id.');

		/*<code-write:You can use factory interface, to generate an instance of Gas object, without instantiate User class>*/$recent_user = Gas::factory('user')->find($new_id);/*<endcode>*/

		// Should be 'foo'
		$this->unit->run($recent_user->username, 'foo', '[get]', 'Retrieve username field.');

		/*<code-write:Suppose you have this $_POST value from some form, to update recent user'>*/$_POST = array('name' => 'Mr. Bar', 'email' => 'bar@world.com');/*<endcode>*/

		/*<code-write:You can still easily attach $_POST using 'fill' method, to set a datas for next updates>*/$recent_user->fill($_POST);/*<endcode>*/

		/*<code-write:You can also set some field directly>*/$recent_user->username = 'bar';/*<endcode>*/

		/*<code-write:You can add additional HTML tag via 'errors' method>*/if ( ! $recent_user->save(TRUE)) die($recent_user->errors('<div class="error">', '</div>'));/*<endcode>*/

		/*<code-write:To delete something, you can directly assign id, or 'delete' will see through your recorded logic, eg : >*/$now_user = Gas::factory('user')->find($new_id);/*<endcode>*/

		// Should be 'bar' now, since we were update it
		$this->unit->run($now_user->username, 'bar', '[get]', 'Retrieve username field.');

		/*<code-write:Just ensure that data has been updated >*/if ($now_user->username != 'bar') die('Gas update was unsuccessfully executed!');/*<endcode>*/

		/*<code-write:This will delete user 1 >*/$now_user->delete();/*<endcode>*/

		/*<code-trans:Create new user instance >*/$user = Gas::factory('user');/*<endcode>*/

		/*<code-trans:This is transaction pointer >*/$user->trans_start();/*<endcode>*/

		/*<code-trans:Lets try to create user 5-9 >*/for($i = 5;$i < 10;$i++)/*<endcode>*/
		/*<code-trans:running query() method >*/{/*<endcode>*/
			/*<code-trans:	Here you can use query() method >*/	$user->query('INSERT INTO `user` (`id`, `name`) VALUES ('.$i.', \'user_'.$i.'\')');/*<endcode>*/
		/*<code-trans:end insert >*/}/*<endcode>*/

		/*<code-trans:Lets try to update above new entries >*/for($i = 5;$i < 10;$i++)/*<endcode>*/
		/*<code-trans:running some AR method >*/{/*<endcode>*/
			/*<code-trans:	Hey, in Gas, we could use any finder too! >*/	$new_user = Gas::factory($user->model())->find($i);/*<endcode>*/
			/*<code-trans:	fill, save and other AR method still available as well! >*/	$new_user->fill(array('name' => 'person_'.$i))->save();/*<endcode>*/
		/*<code-trans:end update >*/}/*<endcode>*/

		/*<code-trans:If something goes wrong >*/if ($user->trans_status() === FALSE)/*<endcode>*/
		/*<code-trans:trans_rollback >*/{/*<endcode>*/
		    /*<code-trans:	will produce SQL : "ROLLBACK" >*/	$user->trans_rollback();/*<endcode>*/
		/*<code-trans:end trans_rollback >*/}/*<endcode>*/
		/*<code-trans:If everything ok >*/else/*<endcode>*/
		/*<code-trans:trans_commmit >*/{/*<endcode>*/
		    /*<code-trans:	will produce SQL : "COMMIT" >*/	$user->trans_commit();/*<endcode>*/
		/*<code-trans:end trans_commmit >*/}/*<endcode>*/

		$user9 = Gas::factory('user')->find(9);

		// Should be an array
		$this->unit->run($user9->name, 'person_9', '[transaction]', 'Transaction test'); 

		$user = new User;

		$user->truncate();

		// Save several datas
		$input_datas = array(

			'valid' => $this->dummy_users,

			'invalid' => array(

				array('id' => 'not number', 'name' => 'more than max length which this field can hold, this is more than 40. Lets make it longggeeeeerrrrrrr. MOOOOOREEEEEE LOOONGGGER', 'email' => 'not[an]email','username' => 'me'),
			)
		);

		foreach ($input_datas as $type => $input_data)
		{
			if ($type == 'valid')
			{
				foreach ($input_data as $post_data)
				{

					$affected_rows = $user->fill($post_data, TRUE)->save(TRUE);

					// Should affect 1 row
					$this->unit->run($affected_rows, 1, '[save]', '$_POST contain : '.implode(', ', $post_data).'.');
				}
			}
			
			elseif ($type == 'invalid')
			{
				foreach ($input_data as $post_data)
				{
					$post_data;

					$affected_rows = $user->fill($post_data, TRUE)->save(TRUE);

					// Should result FALSE
					$this->unit->run($affected_rows, FALSE, '[validation]', 'Error message was : '.$user->errors('<b>','</b>'));
				}
			}
		}
		/*<code-finder:all : will return an array of user's object>*/$users = Gas::factory('user')->all();/*<endcode>*/

		// Should be an array
		$this->unit->run($users, 'is_array', '[all]', 'All methods will return an array of object'); 

		// Should contain 4, because we just adding 4 valid entries.
		$this->unit->run(count($users), 4, '[all]', 'Total records can easily identified, using count array');
		
		foreach ($users as $single_user)
	    {
	    	// Should be an object
	    	$this->unit->run($single_user, 'is_object', '[all]', '-');

	    	// Should be an array
	    	$this->unit->run($single_user->to_array(), 'is_array', '[to_array]', 'Use to_array() method to transform object/records into an array');
	    }


	   	/*<code-finder:first : will return a single object of user which have smallest primary key>*/$firstuser = Gas::factory('user')->first();/*<endcode>*/
	   	
	   	// Should be an object
	    $this->unit->run($firstuser, 'is_object', '[first]', 'first() will return the first record based by primary key');

	    // Should be user with id 1
	    $this->unit->run($firstuser->id, '1', '[first]', '-');
	   
		/*<code-finder:last : will return a single object of user which have bigest primary key>*/$lastuser = Gas::factory('user')->last();/*<endcode>*/

	   	// Should be an object
	    $this->unit->run($lastuser, 'is_object', '[last]', 'last() will return the last record based by primary key');

	    // Should be user with id 1
	    $this->unit->run($lastuser->id, '4', '[last]', '-');
	   	
		/*<code-finder:max : will return a single object of user, which have max value of primary key>*/$max = Gas::factory('user')->max();/*<endcode>*/ 

		// Should be 4
	    $this->unit->run((int)$max->id, 4, '[max]', 'max() will return the highest number of column');

		/*<code-finder:min : will return a single object of user, which have min value of primary key>*/$min = Gas::factory('user')->min();/*<endcode>*/ 

		// Should be 1
	    $this->unit->run((int)$min->id, 1, '[min]', 'min() will return the lowest number of column');

		/*<code-finder:avg : will return a single object of user, which contain average value of primary keys>*/$avg = Gas::factory('user')->avg('id', 'average_id');/*<endcode>*/ 

		// Should be 2.5
	    $this->unit->run((float)$avg->average_id, 2.5000, '[avg]', '(1 + 2 + 3 + 4) / 4 = 2.5 - avg() will return the average number of column');
		
		/*<code-finder:sum : will return a single object of user, which contain summary value of primary keys>*/$sum = Gas::factory('user')->sum('id', 'sum_of_id');/*<endcode>*/ 

		// Should be 10 
	    $this->unit->run((int)$sum->sum_of_id, 10, '[sum]', '1 + 2 + 3 + 4 = 10 - sum() will return the summary number of column');
	
	   	/*<code-finder:find : will return a single object of user, which have value = 1 in primary key>*/$someuser = Gas::factory('user')->find(1);/*<endcode>*/ 

	   	// Should have id 1
		$this->unit->run((int) $someuser->id, 1, '[find]', '-');

		// Should be an object
		$this->unit->run($someuser, 'is_object', '[find]', 'If we assign only one id in find, than it will return an object');

		// Should be 'John Doe'
		$this->unit->run($someuser->name, 'John Doe', '[find]', 'Because user with id 1 is John Doe');

		/*<code-finder:find : will return an array of user's object, which have IN(1, 2, 3) in primary key>*/$someusers = Gas::factory('user')->find(1, 2, 3);/*<endcode>*/ 
		
	   	// Should be an array
		$this->unit->run($someusers, 'is_array', '[find]', 'find() can accept several ids at a time');

		// Should be 3, because we search for 3 valid entries.
		$this->unit->run(count($someusers), 3, '[find]', '-');
		
		$someusers = Gas::factory('user')->find(1, 100, 1000, 10000); 

		// Should be an array
		$this->unit->run($someusers, 'is_array', '[find]', 'Even if the result is one, but it will result an array, because we assign more than one id in find parameter');

		// Should be 1, because we search for 1 valid entries and 3 invalid id.
		$this->unit->run(count($someusers), 1, '[find]', '-');
	   	
		/*<code-finder:find_by_column : will return an array of user's object, where their email are 'johndoe@yahoo.com'>*/$someusers = Gas::factory('user')->find_by_email('johndoe@john.com');/*<endcode>*/

		// Should be an array, because we didnt specify the limit
		$this->unit->run($someusers, 'is_array', '[find_by_something]', 'Without passing limit as second params, Gas will always return an array');

		// Should be 1, because we search for 1 valid entries.
		$this->unit->run(count($someusers), 1, '[find_by_something]', '-');

		/*<code-finder:find_by_column : will return an object of user, where his/her email is 'derekjones@gmail.com', because limit is set to 1>*/$someuser = $user->find_by_email('derekjones@world.com', 1); 

		// Should be an object, because we specify the limit to 1
		$this->unit->run($someuser, 'is_object', '[find_by_something]', 'By passing limit = 1, object will returned instead an array');

		// Should be Derek Jones, because we search for user with id 2.
		$this->unit->run($someuser->name, 'Derek Jones', '[find_by_something]', '-');
		
		/*<code-finder:CI Active Record : will return all user grouped by email>*/$someusers = Gas::factory('user')->group_by('email')->all();/*<endcode>*/

		// Should be an array, because we use 'all'
		$this->unit->run($someusers, 'is_array', '[ci_ar_group_by]', 'Grouped By email');

		// Should be 4, because we use 'all', we just grouped/sorted it by email.
		$this->unit->run(count($someusers), 4, '[ci_ar_group_by]', '-');

		/*<code-finder:CI Active Record : will return all user where their email are like '%world.com%'>*/$someusers = Gas::factory('user')->like('email', 'world.com')->all();/*<endcode>*/

		/*<code-finder:CI Active Record : will return SELECT * FROM (`user`) LEFT JOIN `job` ON `job`.`id` = `user`.`id`>*/$somejoinedusers = Gas::factory('user')->left_join_job('job.id = user.id')->all();/*<endcode>*/

		$this->unit->run($this->db->last_query(), 'SELECT *'."\n"
													.'FROM (`user`)'."\n"
													.'LEFT JOIN `job` ON `job`.`id` = `user`.`id`', 
													'[ci_ar_join]', 'JOIN statement is supported');

		// Should be an array, because we use 'all'
		$this->unit->run($someusers, 'is_array', '[ci_ar_like]', 'Where email like "world.com"');

		// Should be 2, because there are two user created with world email
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

			'comment' => array(

				array('id' => 1, 'parent_id' => 0, 'user_id' => 1, 'description' => 'Comment 1'),

				array('id' => 2, 'parent_id' => 1, 'user_id' => 2, 'description' => 'Reply to comment 1'),

				array('id' => 3, 'parent_id' => 1, 'user_id' => 3, 'description' => 'Another repy to comment 1'),

				array('id' => 4, 'parent_id' => 2, 'user_id' => 1, 'description' => 'Reply to comment 2'),

			),

			'role' => array(

				array('id' => 1, 'name' => 'Administrator', 'description' => 'The Ruler, Administrator have the highest privilege.'),

				array('id' => 2, 'name' => 'Moderator', 'description' => 'Moderator have high privilige.'),

				array('id' => 3, 'name' => 'Member', 'description' => 'Member is a general person, without additional special privilige.'),

			),

			'job_user' => array(

				array('user_id' => 1, 'job_id' => 1),

				array('user_id' => 1, 'job_id' => 2),

				array('user_id' => 2, 'job_id' => 1),

				array('user_id' => 2, 'job_id' => 3),

				array('user_id' => 4, 'job_id' => 4),

			),

			'user_role' => array(

				array('id' => null, 'r_id' => 1, 'u_id' => 1),

				array('id' => null, 'r_id' => 4, 'u_id' => 1),
				
				array('id' => null, 'r_id' => 4, 'u_id' => 2),

			),

		);

		$comment = new Comment;

		$comment->truncate();

		$role = new Role;

		$role->truncate();
		
		$wife = new Wife;

		$wife->truncate();

		$kid = new Kid;

		$kid->truncate();

		$job = new Job;

		$job->truncate();

		$user_role = new User_role;

		$user_role->truncate();

		$this->db->truncate('job_user');

		foreach ($secondary_datas as $type => $input_data)
		{

			if ($type == 'job_user')
			{
				foreach ($input_data as $post_data) $this->db->insert('job_user', $post_data); 
			}
			else
			{
				foreach ($input_data as $post_data)
				{
					$_POST = $post_data;

					$affected_rows = $$type->fill($_POST)->save();

					// Should result TRUE
					$this->unit->run($affected_rows, 1, '[save]', '-');
				}
			}
		}
		   	
		$someuser = Gas::factory('user')->find(1); 

	   	// Should be an object
		$this->unit->run($someuser, 'is_object', '[find]', '-');

		// Should be 'John Doe'
		$this->unit->run($someuser->name, 'John Doe', '[find]', 'Because user with id 1 is John Doe');
		
		// Should be an object, because this is one-to-one relationship
		$this->unit->run($someuser->wife, 'is_object', '[has_one]', 'has_one is properties which define your one-to-one relationship across model/table(s)');

		// Should be Pat Doe
		$this->unit->run($someuser->wife->name, 'Pat Doe', '[has_one]', '-');
		
		/*<code-relations:One-To-One : Will return an object of wife, which have user_id = 1>*/$somewife = Gas::factory('user')->find(1)->wife;/*<endcode>*/

		$somewife->name = 'Patricia Doe';

		// Should be 1 row affected
		$this->unit->run($somewife->save(), 1, '[save]', 'Update wife tables from user model relations');
		
		/*<code-relations:One-To-Many : Will return an array of kid object, which have user_id = 1>*/$somekids = Gas::factory('user')->find(1)->kid;/*<endcode>*/

		// Should be an array, because this is one-to-many relationship
		$this->unit->run($somekids, 'is_array', '[has_many]', 'has_many is properties which define your one-to-many relationship across model/table(s)');

		foreach ($somekids as $kid)
		{
			$contain_family_name = (bool) (strpos($kid->name, 'Doe') !== FALSE);

			// Should be TRUE, because John Doe kid were Daria Doe and John Doe Jr, which contain Doe in their name
			$this->unit->run($contain_family_name, TRUE, '[has_many]', '-');
		}

		/*<code-relations:Through : Will return an array of role object, which have r_id = 1>*/$someroles = Gas::factory('user')->find(1)->role;/*<endcode>*/

		// Should be an array, because this is one-to-many relationship
		$this->unit->run($someroles, 'is_array', '[through]', 'through is properties which define what intermediate table to use in your relationship across model/table(s)');

		/*<code-relations:Self : Will return an array of comment object(replies in this case), which have parent_id = 1>*/$somecomments = Gas::factory('comment')->find(1)->comment;/*<endcode>*/

		// Should be an array, because this is one-to-many relationship
		$this->unit->run($somecomments, 'is_array', '[self]', 'self is properties which define a self-referential within a model/table');

		// Should be an 2, because comment id 1 have 2 comments which represent as its replies
		$this->unit->run(count($somecomments), 2, '[self]', 'self can be your option, while you work on some adjacency data');

		$someuser = Gas::factory('user')->find(4); 
	   	// Should return FALSE
		$this->unit->run(empty($someuser), FALSE, '[find]', '-');
		
		// Should be FALSE, because user 4, didnt have wife
		$this->unit->run($someuser->wife, FALSE, '[has_one]', 'When there is no result, then FALSE will returned');

		// Should be TRUE, because user 4, didnt have kids
		$this->unit->run(empty($someuser->kid), TRUE, '[has_many]', 'When there are no result, then empty array will returned');

		/*<code-relations:Many-To-Many : Will return an array of job object, based by pivot table (job_user), which have user_id = 4>*/$somejobs = Gas::factory('user')->find(4)->job;/*<endcode>*/


		// Should be an array, because this is many-to-many relationship
		$this->unit->run($somejobs, 'is_array', '[has_and_belongs_to]', 'has_and_belongs_to is properties which define your many-to-many relationship across model/table(s)');

		// Should be one, because this user only have one job
		$this->unit->run(count($somejobs), 1, '[has_and_belongs_to]', '-');

		foreach ($somejobs as $job)
		{
			$is_musician = (bool) (strpos($job->description, 'voice') !== FALSE);

			// Should be TRUE, because Chris martin is Coldplay vocalis
			$this->unit->run($is_musician, TRUE, '[has_and_belongs_to]', '-');

			$job->description = 'Only Coldplay can actually called Musician.';

			// Should be 1 row affected, Coldplay is my favourite band
			$this->unit->run($job->save(), 1, '[has_and_belongs_to]', 'Update a many-to-many table');
		}

		/*<code-eagerloading:Eager Loading : Will return an array of user object, alongside with each relational table with WHERE IN(N+)>*/$allinone = Gas::factory('user')->with('wife', 'kid', 'job')->find(1, 2, 3, 4);/*<endcode>*/

		// Should be an array
		$this->unit->run($allinone, 'is_array', '[eager_loading]', '-');

		// Should contain 4, because we just adding 4 valid entries.
		$this->unit->run(count($allinone), 4, '[eager_loading]', '-');

		foreach ($allinone as $one)
		{
			// Should be a string, contain user\'s name
			$this->unit->run($one->name, 'is_string', '[eager_loading]', '-');

			// Should be an object of wife, one-to-one relationship
			if ($one->id != 4) $this->unit->run($one->wife, 'is_object', '[eager_loading]', '-');

			// Should be an array of kid\'s object, one-to-many relationship
			$this->unit->run($one->kid, 'is_array', '[eager_loading]', '-');

			// Should be an array of job\'s object, many-to-many relationship
			$this->unit->run($one->job, 'is_array', '[eager_loading]', '-');

			if ($one->id == 3)
			{
				$some_wife = $one->wife;

				$some_wife->hair_color = 'white';

				// Should be an 1 row affected, because user 3 really have 1 wife
				$this->unit->run($some_wife->save(), 1, '[eager_loading]', '-');
			}
		}

		$someuser = Gas::factory('user')->find(3); 

	   	// Should return FALSE
		$this->unit->run(empty($someuser), FALSE, '[eager_loading]', '-');

		// Should return white, because at loop above, we change it :P
		$this->unit->run($someuser->wife->hair_color, 'white', '[eager_loading]', '-');

		$somewife = $someuser->wife;

		$somewife->hair_color = 'brunette';

		// Should return 1 row affected, because woman with white hair is wick
		$this->unit->run($somewife->save(), 1, '[eager_loading]', '-');
		
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

		$config = $gas->get_config();

		$info[] = '<b>Scanning models directories...</b>';

		foreach ($this->necessary_item as $item)
		{
			if (is_string($config['models_path']))
			{
				$model = APPPATH.$config['models_path'].'/'.$item.$config['models_suffix'].'.php';
			}
			else
			{
				$model = APPPATH.'models/'.$item.$config['models_suffix'].'.php';
			}
			
			if (file_exists($model ))
			{
				$info[] = 'Deleting '.$item.'\'s model : '.$model;
				unlink($model);
			}
		}

		$info[] = "\n".'<b>Scanning views directories...</b>';

		if (file_exists(GAS_UNIT_TEST_VIEW))
		{
			unlink(GAS_UNIT_TEST_VIEW);

			$info[] = 'Deleting : '.GAS_UNIT_TEST_VIEW;
		}

		$all_items = $this->necessary_item; 

		$all_items[] = 'job_user';

		$info[] = "\n".'<b>Scanning database tables...</b>';

		foreach ($all_items as $item) 
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

		foreach ($this->necessary_item as $item)
		{
			$gas->db()->table_exists($item) or $this->_create_table($item);

			$list_models = is_array($gas->list_models()) ? array_keys($gas->list_models()) : array();

			if ( ! in_array($item, $list_models))
			{
				$this->_create_model($item, $gas->get_config());
			}
		}
		
		$gas->db()->table_exists('job_user') or $this->_create_table('job_user');

		if ( ! file_exists(GAS_UNIT_TEST_VIEW)) $this->_create_view();

		return $this->new_state;
	}

	private function _generate_all_tables()
	{
		$tables = array();

		foreach ($this->necessary_item as $item)
		{
			$results = array();

			$instance = new $item;
		
			$instances = $instance->all();

			$data = array($instance->list_fields());

			if ($instance->count() > 0)
			{
				foreach ($instances as $result) $data = array_merge($data, array($result->to_array()));
			}
			else
			{
				$empty = array();

				foreach ($instance->list_fields($instance->table) as $column) $empty[] = '-';

				$data = array_merge($data, array($empty));
			}
			
			$tables[] = heading($item.'\'s table', 4)."\n".$this->table->generate($data);
		}

		return $tables;
	}

	private function _create_model($model, $config, $write = TRUE)
	{
		$this->new_state = TRUE;

		if (is_string($config['models_path']))
		{
			$file = APPPATH.$config['models_path'].'/'.$model.$config['models_suffix'].'.php';
		}
		else
		{
			$file = APPPATH.'models/'.$model.$config['models_suffix'].'.php';
		}

		$user = 'array('."\n"
				."\t\t\t\t".'\'has_one\' => array(\'wife\' => array()),'."\n"
				."\t\t\t\t".'\'has_many\' => array('."\n"
				."\t\t\t\t\t".'\'kid\' => array(),'."\n"
				."\t\t\t\t\t".'\'comment\' => array(),'."\n"
				."\t\t\t\t\t".'\'role\' => array('."\n"
				."\t\t\t\t\t\t".'\'through\' => \'user_role\','."\n"
				."\t\t\t\t\t\t".'\'foreign_key\' => \'u_id\','."\n"
				."\t\t\t\t\t".'),'."\n"
				."\t\t\t\t".'),'."\n"
				."\t\t\t\t".'\'has_and_belongs_to\' => array(\'job\' => array()),'."\n"
				."\t".');';

		$user_role = 'array('."\n"
				."\t\t\t\t".'\'has_one\' => array('."\n"
				."\t\t\t\t\t".'\'user\' => array('."\n"
				."\t\t\t\t\t\t".'\'foreign_key\' => \'u_id\','."\n"
				."\t\t\t\t\t".'),'."\n"
				."\t\t\t\t\t".'\'role\' => array('."\n"
				."\t\t\t\t\t\t".'\'foreign_key\' => \'r_id\','."\n"
				."\t\t\t\t\t".'),'."\n"
				."\t\t\t\t".'),'."\n"
				."\t".');';
		
		$role = 'array('."\n"
				."\t\t\t\t".'\'has_many\' => array('."\n"
				."\t\t\t\t\t".'\'user\' => array('."\n"
				."\t\t\t\t\t\t".'\'through\' => \'user_role\','."\n"
				."\t\t\t\t\t\t".'\'foreign_key\' => \'r_id\','."\n"
				."\t\t\t\t\t".'),'."\n"
				."\t\t\t\t".'),'."\n"
				."\t".');';

		$comment = 'array('."\n"
				."\t\t\t\t".'\'has_many\' => array('."\n"
				."\t\t\t\t\t".'\'comment\' => array('."\n"
				."\t\t\t\t\t\t".'\'self\' => TRUE,'."\n"
				."\t\t\t\t\t\t".'\'foreign_key\' => \'parent_id\','."\n"
				."\t\t\t\t\t".'),'."\n"
				."\t\t\t\t".'),'."\n"
				."\t\t\t\t".'\'belongs_to\' => array(\'user\' => array()),'."\n"
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
				."\t\t\t".'\'username\' => Gas::field(\'char[10]\', array(\'required\', \'callback_username_check\')),'."\n"
				."\t\t".');';

		$user_callback = "\t".'public function username_check($field, $val)'."\n"
				."\t".'{'."\n"
				."\t\t".'if($val == \'me\')'."\n"
				."\t\t".'{'."\n"
				."\t\t\t".'self::set_message(\'username_check\', \'The %s field cannot fill by "me"\', $field);'."\n\n"
				."\t\t\t".'return FALSE;'."\n"
				."\t\t".'}'."\n"
				."\t\t".'return TRUE;'."\n"
				."\t".'}'."\n"
				."\n\n"
				."\t".'function _before_check() {}'."\n"
				."\n\n"
				."\t".'function _after_check() {}'."\n"
				."\n\n"
				."\t".'function _before_save() {}'."\n"
				."\n\n"
				."\t".'function _after_save() {}'."\n"
				."\n\n"
				."\t".'function _before_delete() {}'."\n"
				."\n\n"
				."\t".'function _after_delete() {}'."\n";

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

				'',

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
		
		if ( ! $write)
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

        $comment = array(

					'id' => array(

						'type' => 'INT',

						'constraint' => 3,

						'unsigned' => TRUE,

						'auto_increment' => TRUE,

					),
					'parent_id' => array(

						'type' => 'INT',

						'constraint' => 3,

						'null' => TRUE,

					),
					'user_id' => array(

						'type' => 'INT',

						'constraint' => 3,

					),
                    'description' => array(

                    	'type' => 'TEXT',

                    	'constraint' => 100,

                    	'default' => '',
                    ),
        );

        $role = array(

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

        $user_role = array(

        			'id' => array(

						'type' => 'INT',

						'constraint' => 3,

						'unsigned' => TRUE,

						'auto_increment' => TRUE,

					),
					'u_id' => array(

						'type' => 'INT',

						'constraint' => 3,

					),
                   	'r_id' => array(

						'type' => 'INT',

						'constraint' => 3,

					),
					'active' => array(

						'type' => 'INT',

						'constraint' => 1,

						'default' => 1,

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

   				'a.link{font-weight: bold; color: black; text-decoration: none;}',

   				'a.link:hover{color: #787878;}',

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

			anchor(GAS_NAME.'/index', 'Start Page', $css_anchor),

			anchor(GAS_NAME.'/convention', 'Convention', $css_anchor),

			anchor(GAS_NAME.'/documentation', 'Quick Start', $css_anchor),

			anchor(GAS_NAME.'/extension', 'Extension', $css_anchor),

			anchor(GAS_NAME.'/test_all', 'Run Unit Test', $css_unittest),

			anchor(GAS_NAME.'/end', 'Delete All Files And Tables', $css_exit),

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

		$t = '<span style="color:#E0C61B">';

		$e = '</span>';

		$v = str_replace(array('<?', '=>', '->', ' = ', 'extends', 'public', 'array', 'function', 'class', '<span:comment>'), 

						array('&lt;?', $s.'=>'.$e, $s.'->'.$e, $s.' = '.$e, $a.'extends'.$e, $a.'public'.$e, $a.'array'.$e, $a.'function'.$e, $a.'class'.$e, '<span style="color: #484848; font-style: italic;">'), 

						htmlspecialchars_decode($v));

		if (preg_match_all('/\'([^\']+)\'/', $v, $m) AND count($m) == 2)
		{
			$string_original = array();

			$string_replacement = array();

			foreach ($m[0] as $strings)
			{
				$string_original = $strings;

				$string_replacement[] = $t.$strings.$e;
			}

			$v = str_replace($m[0], $string_replacement, $v);
		}
	}
}