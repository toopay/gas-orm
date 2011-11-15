.. Gas ORM documentation [configuration]

Configuration
=============

Most likely, you wouldn't need to configure anything. But if you care about every millisecond of your application's execution time, or you just felt nerdy, you might want to turn off autoload models or/and autoload extensions. The config file is located at **application/config/gas.php**, and the available configuration are :

Models Path 
++++++++++++

Models path is a directory you are telling Gas ORM to look for your model classes. Generally, it would be on **application/models**. ::

	$config['models_path'] = 'models';

Otherwise, you will need to specify it. 

If you working with **modular directory structure** , you will need to set it to your modules path. For example, if you put your modules directories under **aplication** folder, then you can set **models_path** as bellow : ::

	$config['models_path'] = array(APPPATH.'models', APPPATH.'modules');

This way, will scans both your **application/models** and **application/modules**, for any exists Gas models.

Gas support for cascading directories, so you can have as many sub-level folder as you want, in your primary models directory.

.. note:: If your **modules** directory is not located under application folder, you need to specify the path. You doesn't need to set up each of your module's models directories, just point it to your modules folder, and Gas will start collecting all of your module's models

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

If you decide to turn off autoload models functionality, you can load your model manually like bellow : ::

	Gas::load_model('user');

You can also load several models at a time, by passing an array instead a string.

Autoload Extensions
+++++++++++++++++++

Same with **autoload** models, if you need certain extensions globally throughout your application you should consider auto-loading them for convenience. ::

	$config['autoload_extensions'] = TRUE;

Otherwise, you can always turn it off. 

If you decide to turn off autoload models functionality, you can load your extension manually like bellow : ::

	Gas::load_extension('dummy');

You can also load several extensions at a time, by passing an array instead a string.

Extensions
++++++++++

If you turn on autoload extension option, Gas will only load values inside this array. ::

	$config['extensions'] = array('dummy');

For further information about extension, look at :doc:`Extensions <extension>` section. 