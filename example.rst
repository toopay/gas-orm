.. Gas ORM documentation [example]

An Example Application
=====================

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
	self::$relationships = array(
	
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
				'user'          =>     ORM::belongs_to('\\Model\\User');
			);
			
			self::$fields = array(
				'id' 			=> 		ORM::field('auto[10]'),
				'title' 		=> 		ORM::field('char[255]'),
				'body'	 		=> 		ORM::field('string'),
				'modified_at'	=>		ORM::field('datetime'),
				'created_at'	=>		ORM::field('created_at'),
			);
			
			$this->ts_fields('modified_at','[created_at]');
		}
	}

Note that this is fairly similar to our user model, with a few minor changes.  For instance, our relationship in the blog model is the opposite of the relationship in the user model - user *has_many* blogs, and blogs *belongs_to* user!

There are also a few new types specified in our fields section.  We have a "string" which is the equivalent of a MySQL TEXT field, and two DATETIME fields for storing our creation date and modification date.  The line ::
	$this->ts_fields('modified_at','[created_at]');

Tells Gas that the modified_at should be used for saving our edit datetime, whilst [created_at], because it is inside the square brackets, should be used to save our creation datetime.

With just this simple bit of setup, Gas now has basically everything it needs to start working with our database, but to do this we are going to have to build some controllers and views!
