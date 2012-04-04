.. Gas ORM documentation [example]

An Example Application
========================

OK, so you've read the documentation - especially the sections on conventions, finder and CRUD.  Now you want to see how it all fits together! Well you've come to the right place.  This is a simple example application which follows the standard "hello world" of web programming by building a basic blog application

During this tutorial, we'll build a couple of models, a controller and a few views.  This isn't aimed at somebody who is new to CodeIgniter, but somebody who has used it before and just wants to see how Gas works.


Installing Gas
++++++++++++++

This example isn't going to describe how to install Gas as this is already described in detail in the :doc:`Configuration <configuration>` and :doc:`Quick Start <quickstart>` sections.  If you are having issues getting Gas installed then hop on over to the CodeIgniter forum and ask a few questions.

The Blog Structure
++++++++++++++++++

To start with we are just going to create a simple blog.  There will be blog and user tables (and models), a blog controller with some actions:

+------------------+-------------------------------------------+
|    **Action**    |              **Decription**               |
+==================+===========================================+
|     **view**     | View a blog posting without editing       |
+------------------+-------------------------------------------+
|     **edit**     | Edit an existing blog posting             |
+------------------+-------------------------------------------+
|    **create**    | Create a new blog posting                 |
+------------------+-------------------------------------------+


The Database
++++++++++++

I'm going to assume you know how to connect to a database in CodeIgniter by editing the **config/database.php** file.  Once you have connected properly then you can run the following MySQL script to build the database :  ::

	--
	-- Table structure for table `blog`
	--

	DROP TABLE IF EXISTS `blog`;
	CREATE TABLE IF NOT EXISTS `blog` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) NOT NULL,
	  `title` varchar(255) NOT NULL,
	  `body` text NOT NULL,
	  `modified_at` datetime NOT NULL,
	  `created_at` datetime NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

	-- --------------------------------------------------------

	--
	-- Table structure for table `user`
	--

	DROP TABLE IF EXISTS `user`;
	CREATE TABLE IF NOT EXISTS `user` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `username` varchar(64) NOT NULL,
	  `password` varchar(255) NOT NULL,
	  `email` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



As you can see we have defined a **blog** and a **user** table which will be used to run our simple application.


The Models
+++++++++++

The first thing we need to do is to build our models to describe the blog and user tables.  These should normally reside in your **application/models** folder in your CodeIgniter base directory.  Create a new file here called **user.php** and paste the following code into it : ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {
		
		public $primary_key = 'id';
		
		function _init()
		{
			
			self::$relationships = array (
				'blog'          =>     ORM::has_many('\\Model\\Blog');
			);
			
			self::$fields = array(
				'id' 			=> 		ORM::field('auto[10]'),
				'username' 		=> 		ORM::field('char[64]'),
				'password' 		=> 		ORM::field('char[255]'),
				'email' 		=> 		ORM::field('char[255]'),
			);
		}
	}

This a fairly basic model which describes the user table and doesn't do much else.  The line ::

	public $primary_key = 'id';

Just tells Gas what the name of the primary key is. 

Further down, the relationships that this model has with other models (or just our blog one in this case) are defined using the line that starts with ::

	self::$relationships = array()
	
This tells Gas that the user model is related to the blog model using a 'has_many' relationship.

Finally we define our fields by using ::

	self::$fields = array()

Each field in our database is a record and we have just used the default types - auto for an autoincrement int, and char for varchar.  You can define your types more explicitly, but exactly how to do this is described elsewhere in the documentation.

Next we can follow the same process to define our blog model.  Create a new file in application/models called **blog.php** and paste the following code in : ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class Blog extends ORM {
		
		public $primary_key = 'id';
		
		function _init()
		{
			
			self::$relationships = array (
				'user'          	=>     ORM::belongs_to('\\Model\\User');
			);
			
			self::$fields = array(
				'id' 			=> 		ORM::field('auto[10]'),
				'title' 		=> 		ORM::field('char[255]', array('required','max_length[255]')),
				'body'	 		=> 		ORM::field('string'),
				'modified_at'	=>		ORM::field('datetime'),
				'created_at'	=>		ORM::field('created_at'),
			);
			
			$this->ts_fields('modified_at','[created_at]');
		}
	}

Note that this is fairly similar to our user model, with a few minor changes.  For instance, our relationship in the blog model is the opposite of the relationship in the user model - user *has_many* blogs, and blogs *belongs_to* user!

There are also a few new types specified in our fields section.  We have a "string" which is the equivalent of a MySQL TEXT field, and two DATETIME fields for storing our creation date and modification date.  The line : ::

	$this->ts_fields('modified_at','[created_at]');

tells Gas that the modified_at should be used for saving our edit datetime, whilst [created_at] (because it is inside the square brackets) should be used to save our creation datetime.

Note that we've also placed a second argument inside the title ORM::field section : ::

array('required','max_length[255]')

If you have used CodeIgniter's *form_validation* class then this should be familiar to you - they are basic validation rules for CodeIgniter! We'll come back to this later but basically these allow you to do form_validation with just a simple Gas call to *save()*. 

With just this simple bit of setup, Gas now has basically everything it needs to start working with our database, but to do this we are going to have to build some controllers and views!


Viewing Our Posts
+++++++++++++++++

So we've got our models and our database set up ready to go.  The first thing we want to do is view all our blog posts in the database.  Start by building a controller to handle the requests ::

	<?php if (!defined('BASEPATH')) die ('No direct script access allowed!'); 
	
	class Blog extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
		}

		/*
		 * Displays all of the blog posts in a table
		 */
		public function index()
		{
			// load all of our posts
			$data['posts'] = Model\Blog::all();

			// build our blog table
			$data['content'] = $this->load->view('view_many_posts', $data, TRUE);

			// show the main template
			$this->load->view('main_template', $data, TRUE);
		}
	}

Not a whole lot happening here - we define a controller, call the parent constructor and then build a simple index function which gets all our blog posts from the database and displays them.  The line ::

	$data['posts'] = Model\Blog::all();

Is where all the magic happens.  Note that we can use all the CodeIgniter Active Record calls, so for instance if we wanted the last five created posts we could modify this line to be ::

	$data['posts'] = Model\Blog::limit(5)->order_by('created_at', 'DESC')->all();

Of course you know as well as I do that if we load up http://{your base path}/index.php/blog/ we'll just get a whole bunch of errors saying our views aren't found.  Some basic views we could have are given below: ::

	<!-- view_many_posts.php -->
	<table>
		<thead>
			<tr>
				<th>Post ID</th>
				<th>Post Title</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($posts as $post) : ?>
			<tr>
				<td><?php echo $post->id; ?></td>
				<td><?php echo $post->title; ?></td>
				<td><?php echo anchor('blog/view/'.$post->id,'Read More'); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>		
	</table>
	<!-- End view_many_posts.php -->

Our main template could be something like ::

	<!-- main_template.php -->
	<html>
		<head>
			<title>Our Awesome Blog using GasORM</title>
		</head>
		<body>
			<div id="content">
				<?php echo $content; ?>
			</div>
		</body>
	</html>
	<!-- End main_template.php -->


Creating And Editing A Post
+++++++++++++++++++++++++++

I generally put these in the same basket, as I think it makes for a cleaner and more uniform interface and a minimum of code. Lets add a couple of functions to our controller.  Assume as well that we have a login/auth system that has saved our current user's id at **$this->session->userdata('user_id');** ::

	public function create()
	{
		
	}



Viewing A Single Post
+++++++++++++++++++++

We need to add a little bit to our blog controller to allow us to view a single post ::

	/*
	 * Displays a single posting in detail
	 */
	public function view($id = 0) 
	{
		// start by trying to find a blog object
		// with our passed ID.  If no object is 
		// found, GAS just returns NULL
		$data['post'] = Model\Blog::find($id);

		// check our blog is not null
		if (is_null($data['post'])) 
		{
			show_404();
			return;
		}

		// load the blog post table
		$data['content'] = $this->load->view('view_one_post', $data, TRUE);

		// view the master template
		$this->load->view('template', $data);
	}


Editing A Post
++++++++++++++



Some More Advanced Options
++++++++++++++++++++++++++

What we have done so far only shows a little bit of the power of Gas, but in reality there is a lot more that can be done with this template.  If for instance we wanted to display posts by a given author, we could create a function in our blog controller similar to the *view($id)* function but with our line: 


Replaced by something something like ::

	$data['posts'] = Model\Blog::order_by('created_at','DESC')->find_by_user_id($author_id);

Here we are using the *find_by_column* function where our column is *user_id*, mixed with some CodeIgniter active record code.  Equally, we could use ::

	$data['user'] = Model\User::with('blog')->all();

This *eager loads* a user model joined with the relevant blog records.  We could then access our blog records by calling ::

	$data['posts'] = $data['user']->blog();

Gas also comes bundled with a number of extensions which make building views even easier.  Much of what we did in our *view_many_posts.php* view file can be done with a single line of code from the html extension.  Have a look at the documentation for more information.