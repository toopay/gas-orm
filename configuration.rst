.. Gas ORM documentation [configuration]

Configuration
=============

Most likely, you wouldn't need to configure anything. But if you care about every millisecond of your application's execution time, or you just felt nerdy, you might want to turn off autoload models or/and autoload extensions. The config file is located at **application/config/gas.php**, and the available configuration are :

Models Path 
++++++++++++

Models path is a directory you are telling Gas ORM to look for your model classes. Generally, it would be on **application/models**. ::

	$config['models_path'] = 'models';

Otherwise, you will need to specify it. The value above is relative to your application folder. Gas support for cascading directories, so you can have as many sub-level folder as you want, in your primary models directory.


Models Suffix
++++++++++++++

Models suffix will distinguish your Gas models from your native models, while your native models have **_model** suffix, as default your Gas models will have **_gas** suffix to avoid unintended collision. ::

	$config['models_suffix'] = '_gas';

Otherwise, you will need to specify it.

Autoload Models
++++++++++++++++

Gas ORM comes with an **autoload** feature that permits your models to be loaded automatically every time the system runs. If you need certain models globally throughout your application you should consider auto-loading them for convenience. ::

	$config['autoload_models'] = TRUE;

But Gas ORM will do what you tell it to do. So if you feel loading manually your models is better, you can turn off this option. 

Autoload Extensions
+++++++++++++++++++

Same with **autoload** models, if you need certain extensions globally throughout your application you should consider auto-loading them for convenience. ::

	$config['autoload_extensions'] = TRUE;

Otherwise, you can always turn it off. 

Extensions
++++++++++

If you autoload extension, Gas will only load values inside this array. ::

	$config['extensions'] = array('dummy');

For further information about extension, look at :doc:`Extensions <extension>` section. 