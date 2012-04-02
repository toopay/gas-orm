.. Gas ORM documentation [example]

An Example Application
=====================

OK, so you've read the documentation - especially the sections on conventions, finder and CRUD.  Now you want to see how it all fits together! Well you've come to the right place.  This is a simple example application which follows the standard "hello world" of web programming by building a basic blog application

During this tutorial, we'll build a couple of models, a controller and a few views.  This isn't aimed at somebody who is new to CodeIgniter, but somebody who has used it before and just wants to see how Gas works.


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
++++++++++++++


