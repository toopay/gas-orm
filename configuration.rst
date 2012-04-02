.. Gas ORM documentation [configuration]

Configuration
=============

Most of the time the default Gas configuration works perfectly.  Just in case, the config file is located at **application/config/gas.php**.  The available configuration options are :

Models Path 
++++++++++++

Models path is the directory Gas ORM looks in to find your model classes. Generally, it would be in **application/models**. ::

	$config['models_path'] = array('Model' => APPPATH.'models');

If you use the default value, **Model** will be the namespace of your model(s).

Per-request Cache
+++++++++++++++++

Gas ORM chooses an approach that will optimize your queries. But to make it even better, there are caching request options. Per-request caching basically means no same will be run twice in a single request. It is on by default. ::

	$config['cache_request'] = TRUE;

To turn it off, set this value to FALSE.

You don't have to worry that this caching mechanism might retrieve outdated resources/records. Gas ORM monitoring everything knows exactly what has been changed in your data. This assumes you are using native Gas APIs ( **save** method for insert or update record(s) and **delete** for destroy record) when performing write operations within your database tables. This implies that if you have a **query** method within your Gas models which performs some write actions (in a transaction block for example), you should flush the cache manually. Flushing the cache means you reset the related cache resource. After that the cache mechanism will start caching your resource all over again.

Auto-create models
++++++++++++++++++

Gas ORM supports auto-creation of models based on an existing database schema.  These will be created using standard Gas conventions.  For security reason, this option is disabled by default. To enable : ::

	$config['auto_create_models'] = TRUE;

This option will create all models in the basic state, meaning you will need to add relationships or/and validation rules. This mechanism will also create all of a model's required migrations files.

Since auto-create models mechanism needs to access CodeIgniter migrations configuration, you might need to enable the Migration class in your migration configuration file.

.. note:: Auto-create models feature is intend to help you to make a jumpstart. After you enable this option and successfully generate all files, turn it off.

Auto-create tables
++++++++++++++++++

Gas ORM support auto-creation of tables. This mean you can convert your existing Gas models into a database. For security reasons, this option is disabled by default. To enable : ::

	$config['auto_create_tables'] = TRUE;

This option will create all necesary tables in the basic state, meaning you will need to add relationships, key/indexing or any other task to optimize your database schema. This mechanism will also create all of a model's required migrations files.

Since the auto-create tables mechanism needs to access migrations configuration and run some of its method, you need to enable Migration class in your migration configuration file.

.. note:: Auto-create tables feature is intend to help you to make a jumpstart. After you enable this option and successfully generate all tables, turn it off.
