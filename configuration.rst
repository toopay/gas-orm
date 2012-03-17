.. Gas ORM documentation [configuration]

Configuration
=============

Most likely, you wouldn't need to configure anything. The config file is located at **application/config/gas.php**, and the available configuration are :

Models Path 
++++++++++++

Models path is a directory you are telling Gas ORM to look for your model classes. Generally, it would be on **application/models**. ::

	$config['models_path'] = array('Model' => APPPATH.'models');

If you use this default config, **Model** will be the namespace of your model(s).

Per-request Cache
+++++++++++++++++

Gas ORM has choose an approach that will optimize your queries. But to make it even better, there are caching request option. Per-request caching shortly mean : no same query twice at a request. Its on by default. ::

	$config['cache_request'] = TRUE;

Otherwise, you can always turn it off. 

You doesn't have to worries, that this caching mechanism might retrieves outdated resources/records. Gas ORM monitoring everythings, and it knows exactly what has been changed. But this assumes, you are using its native APIs ( **save** method for insert or update record(s) and **delete** for destroy record) when perform write operations within your database tables. This imply, if you have **query** method within your Gas models which perform some write action (in a transaction block for example), you should flushing the cache manually. Flushing cache mean you reset related cache resource. After that the cache mechanism will start caching your resource all over again.

Auto-create models
++++++++++++++++++

Gas ORM support auto-create models. This mean you can convert your existed database schema into Gas models based by Gas ORM model convention. For security reason, this option is disabled by default. To enable : ::

	$config['auto_create_models'] = TRUE;

This option will create all models in the basic state, mean you will need to tweak its relationships or/and validation rules. This mechanism will also create all model's siblings : migrations files respectively.

Since auto-create models mechanism need to access migrations configuration, you might need to enable Migration class in your migration configuration.

.. note:: Auto-create models feature is intend to help you to make a jumpstart. After you enable this option and successfully generate all files, turn it off.

Auto-create tables
++++++++++++++++++

Gas ORM support auto-create tables. This mean you can convert your existed Gas models into database. For security reason, this option is disabled by default. To enable : ::

	$config['auto_create_tables'] = TRUE;

This option will create all necesary tables in the basic state, mean you will need to tweak its relationships, key/indexing or any other task to optimize your database schema. This mechanism will also create all model's siblings : migrations files respectively.

Since auto-create tables mechanism need to access migrations configuration and run some its method, you need to enable Migration class in your migration configuration.

.. note:: Auto-create tables feature is intend to help you to make a jumpstart. After you enable this option and successfully generate all tables, turn it off.